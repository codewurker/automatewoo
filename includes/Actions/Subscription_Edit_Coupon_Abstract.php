<?php

namespace AutomateWoo;

use AutomateWoo\Actions\Subscriptions\AbstractEditItem;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define shared methods to add, remove or update coupon line items on a subscription.
 *
 * @class Action_Subscription_Edit_Coupon_Abstract
 * @since 4.4
 */
abstract class Action_Subscription_Edit_Coupon_Abstract extends AbstractEditItem {


	/**
	 * Add a coupon selection field to the action's admin UI for store owners to choose what
	 * coupon to edit on the trigger's subscription.
	 *
	 * Optionally also add the quantity input field for the coupon if the instance requires it.
	 */
	public function load_fields() {
		$this->add_coupon_select_field();
	}


	/**
	 * Implement abstract AbstractEditItem method to get the coupon to
	 * edit on a subscription.
	 *
	 * @return \WC_Coupon|false
	 */
	protected function get_object_for_edit() {
		return new \WC_Coupon( $this->get_option( 'coupon' ) );
	}


	/**
	 * Add a coupon selection field for this action
	 */
	protected function add_coupon_select_field() {
		$coupon_select = new Fields\Select();
		$coupon_select->set_required();
		$coupon_select->set_name( 'coupon' );
		$coupon_select->set_title( __( 'Coupon', 'automatewoo' ) );
		$coupon_select->set_options( $this->get_coupons_list() );
		$this->add_field( $coupon_select );
	}

	/**
	 * Get the codes of all non-AutomateWoo coupons.
	 *
	 * @return array Coupon codes (as both key and value of array)
	 */
	protected function get_coupons_list() {
		return Fields_Helper::get_coupons_list();
	}
}
