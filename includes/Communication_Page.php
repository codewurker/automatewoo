<?php
// phpcs:ignoreFile

namespace AutomateWoo;

/**
 * Communication_Page
 * @since 4.0
 */
class Communication_Page {

	/**
	 * Init runs when on the communication preferences page
	 */
	static function init() {
		aw_no_page_cache();
	}


	static function output_preferences_shortcode() {
		$customer = false;
		$customer_key = Clean::string( aw_request( 'customer_key' ) );

		if ( $customer_key ) {
			$customer = Customer_Factory::get_by_key( $customer_key );
		}
		elseif ( is_user_logged_in() ) {
			$customer = Customer_Factory::get_by_user_id( get_current_user_id() );
		}

		ob_start();
		self::output_preferences_form( $customer );
		return ob_get_clean();
	}


	/**
	 * @param Customer $customer
	 */
	static function output_preferences_form( $customer ) {
		$data = [];

		wp_enqueue_style( 'automatewoo-communication-page' );

		$data['intent'] = isset( $_GET['intent'] ) ? Clean::string( $_GET['intent'] ) : false;

		if ( ! $customer ) {
			aw_get_template('communication-preferences/communication-form-no-customer.php', $data );
		}
		else {
			$data[ 'customer' ] = $customer;
			aw_get_template('communication-preferences/communication-form.php', $data );
		}
	}


	static function output_signup_form() {
		wp_enqueue_style( 'automatewoo-communication-page' );
		ob_start();
		aw_get_template('communication-preferences/signup-form.php' );
		return ob_get_clean();
	}

}
