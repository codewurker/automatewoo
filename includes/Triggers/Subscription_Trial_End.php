<?php
// phpcs:ignoreFile

namespace AutomateWoo;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class Trigger_Subscription_Trial_End
 * @since 2.1.0
 */
class Trigger_Subscription_Trial_End extends Trigger {

	/**
	 * Sets supplied data for the trigger.
	 *
	 * @var array
	 */
	public $supplied_data_items = [ 'customer', 'subscription' ];

	
	function load_admin_details() {
		$this->title = __( 'Subscription Trial End', 'automatewoo' );
		$this->group = Subscription_Workflow_Helper::get_group_name();
	}


	function load_fields() {
		$this->add_field( Subscription_Workflow_Helper::get_products_field() );
	}


	function register_hooks() {
		add_action( 'woocommerce_scheduled_subscription_trial_end', [ $this, 'handle_trial_end' ], 20, 1 );
	}

	/**
	 * Handle subscription trial end event.
	 *
	 * @param int $subscription_id
	 */
	function handle_trial_end( $subscription_id ) {
		Subscription_Workflow_Helper::trigger_for_subscription( $this, $subscription_id );
	}


	/**
	 * @param $workflow Workflow
	 * @return bool
	 */
	function validate_workflow( $workflow ) {
		$subscription = $workflow->data_layer()->get_subscription();

		if ( ! $subscription ) {
			return false;
		}

		if ( ! Subscription_Workflow_Helper::validate_products_field( $workflow ) ) {
			return false;
		}

		return true;
	}

}
