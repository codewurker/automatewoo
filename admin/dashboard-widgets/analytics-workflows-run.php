<?php

namespace AutomateWoo;

defined( 'ABSPATH' ) || exit;

/**
 * Dashboard_Widget_Analytics_Workflows_Run class.
 *
 * @since 5.7.0
 */
class Dashboard_Widget_Analytics_Workflows_Run extends Dashboard_Widget_Analytics {

	/**
	 * Widget's ID
	 *
	 * @var string
	 */
	public $id = 'analytics-workflows-run';

	/**
	 * Report page id to be used for "view report" link.
	 *
	 * @var string
	 */
	protected $report_page_id = 'runs-by-date';

	/**
	 * Output the widget content.
	 */
	protected function output_content() {
		if ( ! $this->date_to || ! $this->date_from ) {
			return;
		}

		?>

		<automatewoo-dashboard-chart
			aw-loading
			class="automatewoo-dashboard-chart"
			after="<?php echo esc_js( $this->date_from->format( 'Y-m-d\TH:i:s' ) ); ?>"
			before="<?php echo esc_js( $this->date_to->format( 'Y-m-d\TH:i:s' ) ); ?>"
			fields="runs"
			endpoint="/wc-analytics/reports/workflow-runs/stats"
			interval="<?php echo esc_js( $this->get_interval() ); ?>">

			<div class="automatewoo-dashboard-chart__header">

				<div class="automatewoo-dashboard-chart__header-group">
					<automatewoo-dashboard-chart__header-figure
						class="automatewoo-dashboard-chart__header-figure"
						name="runs">-</automatewoo-dashboard-chart__header-figure>
					<div class="automatewoo-dashboard-chart__header-text">
						<span class="automatewoo-dashboard-chart__legend automatewoo-dashboard-chart__legend--blue"></span>
						<?php esc_html_e( 'workflows run', 'automatewoo' ); ?>
					</div>
				</div>
				<?php $this->output_report_arrow_link(); ?>
			</div>

			<div class="automatewoo-dashboard-chart__tooltip"></div>

			<automatewoo-dashboard-chart__flot
				class="automatewoo-dashboard-chart__flot"><span class="aw-loader">&nbsp;</span></automatewoo-dashboard-chart__flot>

		</automatewoo-dashboard-chart>

		<?php
	}
}

return new Dashboard_Widget_Analytics_Workflows_Run();
