<?php

namespace AutomateWoo\Actions\Subscriptions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Change a subscription's currency.
 *
 * While the currency is not a line item, this class still extends AbstractEditItem
 * as it provides many useful methods for editing a subscription's currency.
 *
 * @since 5.4.0
 */
class UpdateCurrency extends AbstractEditItem {


	/**
	 * Explain to store admin what this action does via a unique title and description.
	 */
	public function load_admin_details() {
		parent::load_admin_details();
		$this->title       = __( 'Update Currency', 'automatewoo' );
		$this->description = __( 'Change a subscription\'s currency. This can be used in the case of major international events, like Brexit. Existing orders will not be updated. Only new orders will have the new currency. No values on the subscription will be updated, use actions to update line items, like products or shipping, to modify those.', 'automatewoo' );
	}


	/**
	 * Add currency selection field to the action's admin UI.
	 */
	public function load_fields() {
		$this->add_currency_code_field();
	}


	/**
	 * Method to get the chosen currency to set on the subscription.
	 *
	 * @return array
	 */
	protected function get_object_for_edit() {
		return $this->get_option( 'currency_code' );
	}


	/**
	 * Set the chosen currency on a subscription.
	 *
	 * @param string           $new_currency_code Currency code. One of the keys from get_woocommerce_currencies(). The return value of @see $this->get_object_for_edit().
	 * @param \WC_Subscription $subscription Instance of the subscription being edited by this action.
	 *
	 * @throws \Exception When there is an error.
	 *
	 * @return bool True if the subscription was edited, false if no change was made.
	 */
	protected function edit_subscription( $new_currency_code, $subscription ) {
		$subscription->set_currency( $new_currency_code );
		$subscription->save();

		return true;
	}


	/**
	 * Get the note to record on the subscription to record the currency change
	 *
	 * @param string $new_currency_code Currency code. One of the keys from get_woocommerce_currencies(). The return value of @see $this->get_object_for_edit().
	 * @return string
	 */
	protected function get_note( $new_currency_code ) {
		/* translators: %1$s: workflow title, %2$s new currency code, %3$d workflow ID */
		return sprintf( __( '%1$s workflow run: updated subscription currency to %2$s. (Workflow ID: %3$d)', 'automatewoo' ), $this->workflow->get_title(), $new_currency_code, $this->workflow->get_id() );
	}


	/**
	 * Add a select field for currency
	 */
	protected function add_currency_code_field() {

		$currency_code_options = get_woocommerce_currencies();

		foreach ( $currency_code_options as $code => $name ) {
			$currency_code_options[ $code ] = $code . ' - ' . $name . ' (' . get_woocommerce_currency_symbol( $code ) . ')';
		}

		asort( $currency_code_options );

		$select = new \AutomateWoo\Fields\Select();
		$select->set_required();
		$select->set_name( 'currency_code' );
		$select->set_title( __( 'New Currency', 'automatewoo' ) );
		$select->set_options( $currency_code_options );
		$select->set_default( get_woocommerce_currency() );

		$this->add_field( $select );
	}
}
