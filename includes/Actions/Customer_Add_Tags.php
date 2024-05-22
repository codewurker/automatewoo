<?php
// phpcs:ignoreFile

namespace AutomateWoo;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class Action_Customer_Add_Tags
 */
class Action_Customer_Add_Tags extends Action {

	public $required_data_items = [ 'customer' ];


	function load_admin_details() {
		$this->title = __( 'Add Tags', 'automatewoo' );
		$this->group = __( 'Customer', 'automatewoo' );
		$this->description = __( 'Please note that tags are not supported on guest customers.', 'automatewoo' );
	}


	function load_fields() {
		$this->add_field( new Fields\User_Tags() );
	}


	function run() {
		if ( ! $customer = $this->workflow->data_layer()->get_customer() ) {
			return;
		}

		$tags = $this->get_option( 'user_tags' );

		if ( ! $customer->is_registered() || empty( $tags ) ) {
			return;
		}

		wp_add_object_terms( $customer->get_user_id(), $tags, 'user_tag' );
	}

}
