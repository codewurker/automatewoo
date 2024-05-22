<?php
// phpcs:ignoreFile

namespace AutomateWoo\Rules;

defined( 'ABSPATH' ) || exit;

/**
 * @class Order_Subscription_Failed_Automatic_Payment_Retry_Count
 */
class Order_Subscription_Failed_Automatic_Payment_Retry_Count extends Abstract_Number {

	public $data_item = 'order';

	public $support_floats = false;


	function init() {
		$this->title = __( 'Order - Subscription Failed Automatic Payment Retry Count', 'automatewoo' );
	}


	/**
	 * @param $order \WC_Order
	 * @param $compare
	 * @param $value
	 * @return bool
	 */
	function validate( $order, $compare, $value ) {
		$failed_retries = \WCS_Retry_Manager::store()->get_retries(
			[
				'status'   => 'failed',
				'order_id' => $order->get_id(),
			],
			'ids'
		);

		return $this->validate_number( count( $failed_retries ), $compare, $value );
	}

}
