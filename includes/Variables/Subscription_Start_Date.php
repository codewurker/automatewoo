<?php
// phpcs:ignoreFile

namespace AutomateWoo;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class Variable_Subscription_Start_Date
 */
class Variable_Subscription_Start_Date extends Variable_Abstract_Datetime {


	function load_admin_details() {
		parent::load_admin_details();
		$this->description = __( "Displays the subscription start date in your website's timezone.", 'automatewoo' );
		$this->description .= ' ' . $this->_desc_format_tip;
	}


	/**
	 * @param $subscription \WC_Subscription
	 * @param $parameters
	 * @return string
	 */
	function get_value( $subscription, $parameters ) {
		$start_date = $subscription->get_date_created();

		if ( Integrations::is_subscriptions_active( '2.4' ) ) {
			$start_date = $subscription->get_date( 'start' );
		}

		return $this->format_datetime( $start_date, $parameters );
	}
}
