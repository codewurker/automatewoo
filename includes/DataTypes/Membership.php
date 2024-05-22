<?php
// phpcs:ignoreFile

namespace AutomateWoo\DataTypes;

use AutomateWoo\Clean;
use AutomateWoo\Integrations;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Membership data type class.
 *
 * @since 2.8.3
 */
class Membership extends AbstractDataType {

	/**
	 * @param $item
	 * @return bool
	 */
	function validate( $item ) {
		return is_a( $item, 'WC_Memberships_User_Membership' );
	}


	/**
	 * @param \WC_Memberships_User_Membership $item
	 * @return mixed
	 */
	function compress( $item ) {
		return $item->get_id();
	}


	/**
	 * @param $compressed_item
	 * @param $compressed_data_layer
	 * @return mixed
	 */
	function decompress( $compressed_item, $compressed_data_layer ) {
		$id = Clean::id( $compressed_item );

		if ( ! Integrations::is_memberships_enabled() || ! $id ) {
			return false;
		}

		$membership = wc_memberships_get_user_membership( $id );

		if ( ! $membership || $membership->get_status() === 'trash' ) {
			return false;
		}

		return $membership;
	}

}
