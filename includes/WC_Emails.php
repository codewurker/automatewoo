<?php
// phpcs:ignoreFile

namespace AutomateWoo;

/**
 * Integration with WC transactional emails.
 *
 * @since 3.8
 */
class WC_Emails {

	/** @var \WC_Email */
	public static $current_email;


	/**
	 * Init
	 */
	static function init() {
		// issue with some template not passing the email in the header hook
		// possibly need to develop a different technique to find this info
//		add_action( 'woocommerce_email_header', [ __CLASS__, 'header' ], 5, 2 );
//		add_action( 'woocommerce_email_footer', [ __CLASS__, 'footer' ], 100 );
	}


	/**
	 * @param $email_heading
	 * @param $email
	 */
	static function header( $email_heading, $email ) {
		if ( $email ) {
			self::$current_email = $email;
		}
	}


	/**
	 * Unset current email property
	 */
	static function footer() {
		self::$current_email = null;
	}


	/**
	 * @return bool
	 */
	static function is_email() {
		return isset( self::$current_email );
	}


	/**
	 * @return \WC_Email
	 */
	static function get_current_email_object() {
		return self::$current_email;
	}


	/**
	 * Returns the email of the current recipient
	 * @return string|false
	 */
	static function get_current_recipient() {
		if ( self::is_email() ) {
			return self::$current_email->recipient;
		}
		return false;
	}


	/**
	 * Returns the email of the current recipient
	 * @return string|false
	 */
	static function is_customer_email() {
		if ( self::is_email() ) {
			return self::$current_email->is_customer_email();
		}
		return false;
	}



}
