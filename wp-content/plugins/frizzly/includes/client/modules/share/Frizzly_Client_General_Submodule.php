<?php

class Frizzly_Client_General_Submodule extends Frizzly_Client_Submodule {

	/**
	 * Frizzly_Client_General_Submodule constructor.
	 *
	 * @param $option Frizzly_Options
	 */
	function __construct($option) {
		parent::__construct( 'general', $option );

		add_action( 'wp_head', array( $this, 'action_head' ) );
	}

	function action_head() {
		if ( is_feed() || ( ! is_single() && ! is_page() ) ) {
			return;
		}
		$post_id = get_the_ID();
		$options = $this->get_submodule_options();
		if ( $options['meta_open_graph'] ) {
			require_once 'meta_tags/Frizzly_Client_Facebook_Meta_Tags.php';
			$fb_meta = new Frizzly_Client_Facebook_Meta_Tags();
			$fb_meta->print_tags( $post_id );
		}
		if ( $options['meta_twitter'] ) {
			require_once 'meta_tags/Frizzly_Client_Twitter_Meta_Tags.php';
			$tw_meta = new Frizzly_Client_Twitter_Meta_Tags();
			$tw_meta->print_tags( $post_id, $options );
		}
	}

	function is_active() {
		return true;
	}

	function get_i18n() {
		$sharer_action_name = 'frizzly_share_by_email';

		return array(
			'email_sharer' => array(
				'ajax_action'      => $sharer_action_name,
				'ajax_nonce'       => wp_create_nonce( $sharer_action_name ),
				'ajax_url'         => admin_url( 'admin-ajax.php' ),
				'targetEmailLabel' => __( 'Send to Email Address', 'frizzly' ),
				'sourceEmailLabel' => __( 'Your Email Address', 'frizzly' ),
				'sourceNameLabel'  => __( 'Your Name', 'frizzly' ),
				'button'           => __( 'Send Email', 'frizzly' ),
				'unknown_error'    => __( 'Unknown error. Sharing failed.', 'frizzly' )
			)
		);
	}
}