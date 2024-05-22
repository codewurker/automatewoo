<?php
// phpcs:ignoreFile

namespace AutomateWoo\Rules;

use AutomateWoo\RuleQuickFilters\Clauses\ClauseInterface;
use AutomateWoo\Rules\Interfaces\QuickFilterable;
use AutomateWoo\Rules\Utilities\ArrayQuickFilter;
use AutomateWoo\Subscription_Workflow_Helper;
use Exception;

defined( 'ABSPATH' ) || exit;

/**
 * @class Subscription_Status
 */
class Subscription_Status extends Preloaded_Select_Rule_Abstract implements QuickFilterable {

	use ArrayQuickFilter;

	public $data_item = 'subscription';


	function init() {
		parent::init();

		$this->title = __( 'Subscription - Status', 'automatewoo' );
	}


	function load_select_choices() {
		return Subscription_Workflow_Helper::get_subscription_statuses();
	}


	/**
	 * @param $subscription \WC_Subscription
	 * @param $compare
	 * @param $value
	 * @return bool
	 */
	function validate( $subscription, $compare, $value ) {
		return $this->validate_select( 'wc-' . $subscription->get_status(), $compare, $value );
	}

	/**
	 * Get quick filter clause.
	 *
	 * @since 5.0.0
	 *
	 * @param string $compare_type
	 * @param array  $value
	 *
	 * @return ClauseInterface
	 *
	 * @throws Exception When there is an error.
	 */
	public function get_quick_filter_clause( $compare_type, $value ) {
		return $this->generate_array_quick_filter_clause( 'status', $compare_type, $value );
	}

}
