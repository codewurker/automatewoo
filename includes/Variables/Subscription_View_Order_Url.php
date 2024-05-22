<?php
// phpcs:ignoreFile

namespace AutomateWoo;

use AutomateWoo\Frontend_Endpoints\Login_Redirect;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class Variable_Subscription_View_Order_Url
 */
class Variable_Subscription_View_Order_Url extends Variable {


	function load_admin_details() {
		$this->description = __( "Displays a URL to the subscription page in the My Account area.", 'automatewoo');
	}


	/**
	 * @param $subscription \WC_Subscription
	 * @param $parameters
	 * @return string
	 */
	function get_value( $subscription, $parameters ) {
		return ( new Login_Redirect() )->get_login_redirect_url( $subscription->get_view_order_url() );
	}

}
