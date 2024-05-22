<?php
// phpcs:ignoreFile

namespace AutomateWoo\DataTypes;

use AutomateWoo\Clean;
use AutomateWoo\Integrations;
use WC_Subscription;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Subscription data type class.
 */
class Subscription extends AbstractDataType {


	/**
	 * @param $item
	 * @return bool
	 */
	function validate( $item ) {
		return $item instanceof WC_Subscription;
	}


	/**
	 * @param WC_Subscription $item
	 * @return mixed
	 */
	function compress( $item ) {
		return $item->get_id();
	}


	/**
	 * @param $compressed_item
	 * @param $compressed_data_layer
	 * @return WC_Subscription|false
	 */
	function decompress( $compressed_item, $compressed_data_layer ) {
		$id = Clean::id( $compressed_item );

		if ( ! Integrations::is_subscriptions_active() || ! $id ) {
			return false;
		}

		$subscription = wcs_get_subscription( $id );

		if ( ! $subscription || $subscription->get_status() === 'trash' ) {
			return false;
		}

		return $subscription;
	}

	/**
	 * Get singular name for data type.
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public function get_singular_name() {
		return __( 'Subscription', 'automatewoo' );
	}

	/**
	 * Get plural name for data type.
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public function get_plural_name() {
		return __( 'Subscriptions', 'automatewoo' );
	}

}
