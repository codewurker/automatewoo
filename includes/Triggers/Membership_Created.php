<?php
// phpcs:ignoreFile

namespace AutomateWoo;

use AutomateWoo\Async_Events\MembershipCreated as MembershipCreatedEvent;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class Trigger_Membership_Created
 * @since 2.9
 */
class Trigger_Membership_Created extends Trigger_Abstract_Memberships {

	/**
	 * Async events required by the trigger.
	 *
	 * @since 5.2.0
	 * @var array|string
	 */
	protected $required_async_events = MembershipCreatedEvent::NAME;

	public $_membership_created_via_admin;


	function load_admin_details() {
		parent::load_admin_details();
		$this->title = __( 'Membership Created', 'automatewoo' );
	}


	function load_fields() {
		$plans_field = $this->get_field_membership_plans();
		$this->add_field( $plans_field );
	}


	function register_hooks() {
		if ( is_admin() ) {
			add_action( 'transition_post_status', [ $this, 'transition_post_status' ], 50, 3 );
		}

		$async_event = Async_Events::get( MembershipCreatedEvent::NAME );
		if ( $async_event ) {
			add_action( $async_event->get_hook_name(), [ $this, 'handle_membership_created_async' ] );
		}
	}


	/**
	 * @param $new_status
	 * @param $old_status
	 * @param \WP_Post $post
	 */
	function transition_post_status( $new_status, $old_status, $post ) {
		if ( $old_status === 'auto-draft' && $post->post_type === 'wc_user_membership' ) {
			// don't trigger now as post transition happens before data is saved
			$this->_membership_created_via_admin = $post->ID;
			add_action( 'wc_memberships_user_membership_saved', [ $this, 'membership_created_via_admin' ], 100, 2 );
		}
	}


	/**
	 * @param \WC_Memberships_Membership_Plan $plan
	 * @param $args
	 */
	function membership_created_via_admin( $plan, $args ) {
		// check the created membership is a match
		if ( $this->_membership_created_via_admin == $args['user_membership_id'] ) {
			$this->maybe_run_for_membership( (int) $args['user_membership_id'] );
		}
	}
	
	/**
	 * Handle async membership created event.
	 *
	 * @param int|string $membership_id
	 */
	public function handle_membership_created_async( $membership_id ) {
		$this->maybe_run_for_membership( (int) $membership_id );
	}

	/**
	 * Maybe run trigger for a given membership.
	 *
	 * @param int $membership_id
	 */
	protected function maybe_run_for_membership( int $membership_id ) {
		$membership = wc_memberships_get_user_membership( $membership_id );
		if ( ! $membership ) {
			return;
		}

		$this->maybe_run(
			[
				'membership' => $membership,
				'customer'   => Customer_Factory::get_by_user_id( $membership->get_user_id() )
			]
		);
	}


	/**
	 * @param Workflow $workflow
	 *
	 * @return bool
	 */
	function validate_workflow( $workflow ) {
		$membership = $workflow->data_layer()->get_membership();
		$plans = $workflow->get_trigger_option( 'membership_plans' );

		if ( ! $membership ) {
			return false;
		}

		if ( ! empty( $plans ) ) {
			if ( ! in_array( $membership->get_plan_id(), $plans ) ) {
				return false;
			}
		}

		return true;
	}


}
