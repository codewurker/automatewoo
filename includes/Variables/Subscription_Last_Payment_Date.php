<?php
// phpcs:ignoreFile

namespace AutomateWoo;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class Variable_Subscription_Last_Payment_Date
 */
class Variable_Subscription_Last_Payment_Date extends Variable_Abstract_Datetime {


	function load_admin_details() {
		parent::load_admin_details();
		$this->description = __( "Displays the date of the most recent payment for the subscription.", 'automatewoo' );
		$this->description .= ' ' . $this->_desc_format_tip;
	}


	/**
	 * @param $subscription \WC_Subscription
	 * @param $parameters
	 * @return string
	 */
	function get_value( $subscription, $parameters ) {
		return $this->format_datetime( $subscription->get_date( 'last_payment', 'site' ), $parameters );
	}
}
