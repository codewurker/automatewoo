<?php
// phpcs:ignoreFile

namespace AutomateWoo;

defined( 'ABSPATH' ) || exit;

/**
 * @class Action_Subscription_Update_Meta
 * @since 4.2
 */
class Action_Subscription_Update_Meta extends Action_Order_Update_Meta {

	public $required_data_items = [ 'subscription' ];


	function load_admin_details() {
		$this->title       = __( 'Update Custom Field', 'automatewoo' );
		$this->group       = __( 'Subscription', 'automatewoo' );
		$this->description = __( 'This action can add or update a subscription\'s custom field. Please note that it should not be used to update internal fields like the subscription schedule.', 'automatewoo' );
	}


	function run() {
		if ( ! $subscription = $this->workflow->data_layer()->get_subscription() ) {
			return;
		}

		$meta_key   = trim( $this->get_option( 'meta_key', true ) );
		$meta_value = $this->get_option( 'meta_value', true );

		// Make sure there is a meta key but a value is not required
		if ( $meta_key ) {
			$subscription->update_meta_data( $meta_key, $meta_value );
			$subscription->save();
		}

	}

}
