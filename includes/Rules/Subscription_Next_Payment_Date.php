<?php

namespace AutomateWoo\Rules;

use AutomateWoo\RuleQuickFilters\Clauses\ClauseInterface;
use AutomateWoo\Rules\Interfaces\QuickFilterable;
use AutomateWoo\Rules\Utilities\DateQuickFilter;
use Exception;

defined( 'ABSPATH' ) || exit;

/**
 * Subscription next payment date rule.
 *
 * @class Subscription_Next_Payment_Date
 */
class Subscription_Next_Payment_Date extends Abstract_Date implements QuickFilterable {

	use DateQuickFilter;

	/**
	 * Data item.
	 *
	 * @var string
	 */
	public $data_item = 'subscription';

	/**
	 * Subscription_Next_Payment_Date constructor.
	 */
	public function __construct() {
		$this->has_is_future_comparision = true;

		parent::__construct();
	}

	/**
	 * Init.
	 */
	public function init() {
		$this->title = __( 'Subscription - Next Payment Date', 'automatewoo' );
	}


	/**
	 * Validates our rule.
	 *
	 * @param \WC_Subscription $subscription The subscription object.
	 * @param string           $compare      Rule to compare.
	 * @param array|null       $value        The values we have to compare. Null is only allowed when $compare is is_not_set.
	 *
	 * @return bool
	 */
	public function validate( $subscription, $compare, $value = null ) {
		return $this->validate_date( $compare, $value, aw_normalize_date( $subscription->get_date( 'next_payment' ) ) );
	}

	/**
	 * Get quick filter clause for this rule.
	 *
	 * @since 5.0.0
	 *
	 * @param string $compare_type
	 * @param mixed  $value
	 *
	 * @return ClauseInterface
	 *
	 * @throws Exception When there is an error.
	 */
	public function get_quick_filter_clause( $compare_type, $value ) {
		return $this->generate_date_quick_filter_clause( 'next_payment_date', $compare_type, $value );
	}
}
