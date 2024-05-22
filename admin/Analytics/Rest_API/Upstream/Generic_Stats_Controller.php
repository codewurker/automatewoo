<?php
namespace AutomateWoo\Admin\Analytics\Rest_API\Upstream;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\ParameterException;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * This is a generic class, to cover bits shared by all reports.
 * Discovered in https://github.com/woocommerce/automatewoo/pull/1226#pullrequestreview-1210449142
 * We may consider moving it eventually to `WC_REST_Reports_Controller`,
 * so the other extensions and WC itself could make use of it, and get DRYier.
 * https://github.com/woocommerce/automatewoo/issues/1238
 */
class Generic_Stats_Controller extends Generic_Controller {

	/**
	 * Get all reports.
	 *
	 * @param WP_REST_Request $request Request data.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_items( $request ) {
		$query_args = $this->prepare_reports_query( $request );
		$query      = $this->construct_query( $query_args );
		try {
			$report_data = $query->get_data();
		} catch ( ParameterException $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}

		$out_data = array(
			'totals'    => $report_data->totals ? get_object_vars( $report_data->totals ) : null,
			'intervals' => array(),
		);

		foreach ( $report_data->intervals as $interval_data ) {
			$item                    = $this->prepare_item_for_response( (object) $interval_data, $request );
			$out_data['intervals'][] = $this->prepare_response_for_collection( $item );
		}

		return $this->add_pagination_headers(
			$request,
			$out_data,
			(int) $report_data->total,
			(int) $report_data->page_no,
			(int) $report_data->pages
		);
	}

	/**
	 * Get the Report's schema, conforming to JSON Schema.
	 *
	 * Compatibility-code "WC<=7.8"
	 * Once WC > 7.8 is out and covers our L-2, we can inherit this from `GenericController`.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$data_values = $this->get_item_properties_schema();

		$segments = array(
			'segments' => array(
				'description' => __( 'Reports data grouped by segment condition.', 'automatewoo' ),
				'type'        => 'array',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'segment_id' => array(
							'description' => __( 'Segment identificator.', 'automatewoo' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'subtotals'  => array(
							'description' => __( 'Interval subtotals.', 'automatewoo' ),
							'type'        => 'object',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
							'properties'  => $data_values,
						),
					),
				),
			),
		);

		$totals = array_merge( $data_values, $segments );

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'report_stats',
			'type'       => 'object',
			'properties' => array(
				'totals'    => array(
					'description' => __( 'Totals data.', 'automatewoo' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
					'properties'  => $totals,
				),
				'intervals' => array(
					'description' => __( 'Reports data grouped by intervals.', 'automatewoo' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'interval'       => array(
								'description' => __( 'Type of interval.', 'automatewoo' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
								'enum'        => array( 'day', 'week', 'month', 'year' ),
							),
							'date_start'     => array(
								'description' => __( "The date the report start, in the site's timezone.", 'automatewoo' ),
								'type'        => 'string',
								'format'      => 'date-time',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
							'date_start_gmt' => array(
								'description' => __( 'The date the report start, as GMT.', 'automatewoo' ),
								'type'        => 'string',
								'format'      => 'date-time',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
							'date_end'       => array(
								'description' => __( "The date the report end, in the site's timezone.", 'automatewoo' ),
								'type'        => 'string',
								'format'      => 'date-time',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
							'date_end_gmt'   => array(
								'description' => __( 'The date the report end, as GMT.', 'automatewoo' ),
								'type'        => 'string',
								'format'      => 'date-time',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
							'subtotals'      => array(
								'description' => __( 'Interval subtotals.', 'automatewoo' ),
								'type'        => 'object',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
								'properties'  => $totals,
							),
						),
					),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get the query params for collections.
	 *
	 * You may consider extending it with `segmentby`, with:
	 * ```
	 * $params = parent::get_collection_params();
	 * $params['segmentby'] = array(
	 *     'description'       => __( 'Segment the response by additional constraint.', 'my-extension' ),
	 *     'type'              => 'string',
	 *     'enum'              => array(
	 *         'property_name',
	 *     ),
	 *     'validate_callback' => 'rest_validate_request_arg',
	 * );
	 * ```
	 *
	 * Compatibility-code "WC<
	 * Once WC > 7.8 is out and covers our L-2, we can inherit this from `GenericController`.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['interval'] = array(
			'description'       => __( 'Time interval to use for buckets in the returned data.', 'automatewoo' ),
			'type'              => 'string',
			'default'           => 'week',
			'enum'              => array(
				'hour',
				'day',
				'week',
				'month',
				'quarter',
				'year',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['fields']   = array(
			'description'       => __( 'Limit stats fields to the specified items.', 'automatewoo' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_slug_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'type' => 'string',
			),
		);

		return $params;
	}
}
