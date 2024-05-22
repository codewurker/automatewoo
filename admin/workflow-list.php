<?php
// phpcs:ignoreFile

namespace AutomateWoo;

use AutomateWoo\Workflows\TimingDescriptionGenerator;
use AutomateWoo\Workflows\Factory;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class Admin_Workflow_List
 * @since 2.6.1
 */
class Admin_Workflow_List {

	/**
	 * Constructor
	 */
	function __construct() {

		add_filter( 'manage_posts_columns' , [ $this, 'columns'] );
		add_filter( 'manage_posts_custom_column' , [ $this, 'column_data'], 10 , 2 );
		add_filter( 'bulk_actions-edit-aw_workflow' , [ $this, 'bulk_actions' ], 10 , 2 );
		add_filter( 'post_row_actions' , [ $this, 'row_actions' ], 10 , 2 );
		add_filter( 'request', [ $this, 'filter_request_query_vars' ] );
		add_filter( 'views_edit-aw_workflow', [ $this, 'filter_views' ] );

		$this->statuses();
	}


	/**
	 * @param $columns
	 * @return array
	 */
	function columns( $columns ) {

		unset( $columns['date'] );
		unset( $columns['stats'] );
		unset( $columns['likes'] );

		$columns['timing'] = __( 'Timing', 'automatewoo' );
		$columns['times_run'] = __( 'Run Count', 'automatewoo' );
		$columns['queued'] = __( 'Queue Count', 'automatewoo' );
		$columns['aw_status_toggle'] = '';

		return $columns;
	}


	/**
	 * @param $column
	 * @param $post_id
	 */
	function column_data( $column, $post_id ) {
		$workflow = Factory::get( $post_id );

		if ( ! $workflow )
			return;

		switch ( $column ) {

			case 'timing':
				echo $this->get_timing_text( $workflow );
				break;

			case 'times_run':
				if ( $count = $workflow->get_times_run() ) {
					echo '<a href="' . esc_url( add_query_arg( '_workflow', $workflow->get_id(), Admin::page_url( 'logs' ) ) ) . '">' . $count . '</a>';
				}
				else {
					echo '-';
				}
				break;

			case 'queued':
				if ( $count = $workflow->get_current_queue_count() ) {
					echo '<a href="' . esc_url( add_query_arg( '_workflow', $workflow->get_id(), Admin::page_url( 'queue' ) ) ) . '">' . $count . '</a>';
				}
				else {
					echo '-';
				}
				break;

			case 'aw_status_toggle':
				if ( 'manual' === $workflow->get_type() ) {
					$url = Admin::page_url( 'manual-workflow-runner', $workflow->get_id() );
					printf(
						'<a href="%s" class="button button-primary alignright">%s</a>',
						esc_url( $url ),
						esc_html__( 'Run', 'automatewoo' )
					);
				} else {
					printf(
						'<button type="button" class="%s" data-workflow-id="%s" data-aw-switch="%s">%s</button>',
						'aw-switch js-toggle-workflow-status',
						esc_attr( $workflow->get_id() ),
						esc_attr( $workflow->is_active() ? 'on' : 'off' ),
						esc_html__( 'Toggle Status', 'automatewoo' )
					);
				}
				break;

		}
	}


	/**
	 * Tweak workflow statuses
	 */
	function statuses() {

		global $wp_post_statuses;

		// rename published
		$wp_post_statuses['publish']->label_count = _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'automatewoo' );

		$trash = $wp_post_statuses['trash'];
		unset( $wp_post_statuses['trash'] );
		$wp_post_statuses['trash'] = $trash;
	}


	/**
	 * @param $actions
	 * @return mixed
	 */
	function bulk_actions( $actions ) {
		unset($actions['edit']);
		return $actions;
	}


	/**
	 * @param $actions
	 * @return mixed
	 */
	function row_actions( $actions ) {
		unset($actions['inline hide-if-no-js']);
		return $actions;
	}


	/**
	 * @param Workflow $workflow
	 * @return string
	 */
	function get_timing_text( $workflow ) {
		try {
			return ( new TimingDescriptionGenerator( $workflow ) )->generate();
		} catch ( \Exception $e ) {
			return '-';
		}
	}


	/**
	 * Is manual view?
	 *
	 * @since 5.0.0
	 *
	 * @return bool
	 */
	public function is_manual_view() {
		return (bool) aw_get_url_var( 'filter_manual' );
	}

	/**
	 * Filter workflow list main request query vars.
	 *
	 * @param array $query_vars
	 *
	 * @return array
	 */
	public function filter_request_query_vars( $query_vars ) {
		$is_all_view = empty( $query_vars['post_status'] );

		// Include disabled workflows in all view
		if ( $is_all_view ) {
			$query_vars['post_status'] = [ 'publish', 'aw-disabled' ];
		}

		if ( $this->is_manual_view() ) {
			$query_vars['meta_query'] = [
				[
					'key'   => 'type',
					'value' => 'manual',
				],
			];
		}

		return $query_vars;
	}

	/**
	 * Filter views on the workflow list table.
	 *
	 * @since 5.0.0
	 *
	 * @param array $views
	 *
	 * @return array
	 */
	public function filter_views( $views ) {
		$url = remove_query_arg( 'post_status', add_query_arg( 'filter_manual', 1 ) );

		$views['manual'] = sprintf(
			'<a href="%s" class="%s">%s <span class="count">(%s)</a>',
			esc_url( $url ),
			$this->is_manual_view() ? esc_attr( 'current' ) : '',
			esc_html__( 'Manual', 'automatewoo' ),
			Workflows::get_manual_workflows_count()
		);

		$trash = aw_array_extract( $views, 'trash' );
		if ( $trash ) {
			$views['trash'] = $trash;
		}

		return $views;
	}

}
