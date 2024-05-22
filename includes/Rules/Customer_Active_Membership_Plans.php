<?php
// phpcs:ignoreFile

namespace AutomateWoo\Rules;

use AutomateWoo\DataTypes\DataTypes;
use AutomateWoo\Memberships_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * @class Customer_Active_Membership_Plans
 */
class Customer_Active_Membership_Plans extends Preloaded_Select_Rule_Abstract {

	public $data_item = DataTypes::CUSTOMER;

	public $is_multi = true;


	function init() {
		parent::init();

		$this->title = __( "Customer - Active Memberships Plans", 'automatewoo' );
	}


	/**
	 * @return array
	 */
	function load_select_choices() {
		return Memberships_Helper::get_membership_plans();
	}


	/**
	 * @param $customer \AutomateWoo\Customer
	 * @param $compare
	 * @param $value
	 * @return bool
	 */
	function validate( $customer, $compare, $value ) {
		$active_plans = [];

		if ( $customer->is_registered() ) {
			foreach( wc_memberships_get_user_active_memberships( $customer->get_user_id() ) as $membership ) {
				$active_plans[] = $membership->get_plan_id();
			}
		}

		return $this->validate_select( $active_plans, $compare, $value );
	}

}
