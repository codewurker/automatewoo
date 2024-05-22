<?php
namespace AutomateWoo\Admin\Analytics\Rest_API\Upstream;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\Query;
use stdClass;
use WC_REST_Reports_Controller;
use WP_REST_Request;
use WP_REST_Response;

/**
 * This is a generic class, to cover bits shared by all reports.
 * Discovered in https://github.com/woocommerce/automatewoo/pull/1226#pullrequestreview-1210449142
 * We may consider moving it eventually to `WC_REST_Reports_Controller`,
 * so the other extensions and WC itself could make use of it, and get DRYier.
 * https://github.com/woocommerce/automatewoo/issues/1238
 *
 * @extends WC_REST_Reports_Controller
 */
class Generic_Controller extends WC_REST_Reports_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * Compatibility-code "WC <= 7.8"
	 * Once WC > 7.8 is out and covers our L-2, we can inherit this from `GenericController`.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-analytics';

	/**
	 * Forwards a Query constructor,
	 * to be able to customize Query class for a specific report.
	 *
	 * @param array $query_args Set of args to be forwarded to the constructor.
	 * @return Query
	 */
	protected function construct_query( $query_args ) {
		return new Query( $query_args );
	}

	/**
	 * Maps query arguments from the REST request.
	 *
	 * `WP_REST_Request` does not expose a method to return all params covering defaults,
	 * as it does for `$request['param']` accessor.
	 * Therefore, we re-implement defaults resolution.
	 *
	 * @param WP_REST_Request $request Full request object.
	 * @return array Simplified array of params.
	 */
	protected function prepare_reports_query( $request ) {
		$args = wp_parse_args(
			array_intersect_key(
				$request->get_query_params(),
				$this->get_collection_params()
			),
			$request->get_default_params()
		);

		return $args;
	}

	/**
	 * Add pagination headers and links.
	 *
	 * Compatibility-code "WC <= 7.8"
	 * Once WC > 7.8 is out and covers our L-2, we can inherit this from `GenericController`.
	 *
	 * @param WP_REST_Request        $request   Request data.
	 * @param WP_REST_Response|array $response  Response data.
	 * @param int                    $total     Total results.
	 * @param int                    $page      Current page.
	 * @param int                    $max_pages Total amount of pages.
	 * @return WP_REST_Response
	 */
	public function add_pagination_headers( $request, $response, int $total, int $page, int $max_pages ) {
		$response = rest_ensure_response( $response );
		$response->header( 'X-WP-Total', $total );
		$response->header( 'X-WP-TotalPages', $max_pages );

		// SEMGREP WARNING EXPLANATION
		// URL is escaped. However, Semgrep only considers esc_url as valid.
		$base = esc_url_raw(
			add_query_arg(
				$request->get_query_params(),
				rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) )
			)
		);

		if ( $page > 1 ) {
			$prev_page = $page - 1;
			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}
			// SEMGREP WARNING EXPLANATION
			// URL is escaped. However, Semgrep only considers esc_url as valid.
			$prev_link = esc_url_raw( add_query_arg( 'page', $prev_page, $base ) );
			$response->link_header( 'prev', $prev_link );
		}

		if ( $max_pages > $page ) {
			$next_page = $page + 1;
			// SEMGREP WARNING EXPLANATION
			// URL is escaped. However, Semgrep only considers esc_url as valid.
			$next_link = esc_url_raw( add_query_arg( 'page', $next_page, $base ) );
			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	/**
	 * Prepare a report object for serialization.
	 *
	 * Compatibility-code "WC <= 7.8"
	 * Once WC > 7.8 is out and covers our L-2, we can inherit this from `GenericController`.
	 *
	 * @param stdClass        $report  Report data.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_item_for_response( $report, $request ) {
		$data = get_object_vars( $report );

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Get the Report's item properties schema.
	 * Will be used by `get_item_schema` as `totals` and `subtotals`.
	 *
	 * To be extended by specific report properites.
	 *
	 * Compatibility-code "WC <= 7.8"
	 * Once WC > 7.8 is out and covers our L-2, we can inherit this from `GenericController`.
	 *
	 * @return array
	 */
	public function get_item_properties_schema() {
		return array();
	}

	/**
	 * Get the Report's schema, conforming to JSON Schema.
	 *
	 * To be extended for each report.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		return $this->add_additional_fields_schema( array() );
	}

	/**
	 * Get the query params for collections.
	 *
	 * Compatibility-code "WC <= 7.8"
	 * Once WC > 7.8 is out and covers our L-2, we can inherit this from `GenericController`.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params                        = array();
		$params['context']             = $this->get_context_param( array( 'default' => 'view' ) );
		$params['page']                = array(
			'description'       => __( 'Current page of the collection.', 'automatewoo' ),
			'type'              => 'integer',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
			'minimum'           => 1,
		);
		$params['per_page']            = array(
			'description'       => __( 'Maximum number of items to be returned in result set.', 'automatewoo' ),
			'type'              => 'integer',
			'default'           => 10,
			'minimum'           => 1,
			'maximum'           => 100,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['after']               = array(
			'description'       => __( 'Limit response to resources published after a given ISO8601 compliant date.', 'automatewoo' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['before']              = array(
			'description'       => __( 'Limit response to resources published before a given ISO8601 compliant date.', 'automatewoo' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['order']               = array(
			'description'       => __( 'Order sort attribute ascending or descending.', 'automatewoo' ),
			'type'              => 'string',
			'default'           => 'desc',
			'enum'              => array( 'asc', 'desc' ),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['orderby']             = array(
			'description'       => __( 'Sort collection by object attribute.', 'automatewoo' ),
			'type'              => 'string',
			'default'           => 'date',
			'enum'              => array(
				'date',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['force_cache_refresh'] = array(
			'description'       => __( 'Force retrieval of fresh data instead of from the cache.', 'automatewoo' ),
			'type'              => 'boolean',
			'sanitize_callback' => 'wp_validate_boolean',
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $params;
	}
}
