<?php

abstract class Frizzly_Ajax_Handler {

	public $action_name;

	function __construct( $action_name ) {
		$this->action_name = 'frizzly_' . $action_name;
		add_action( 'wp_ajax_nopriv_' . $this->action_name, array( $this, 'handle' ) );
		add_action( 'wp_ajax_' . $this->action_name, array( $this, 'handle' ) );
	}

	function handle() {
		check_ajax_referer( $this->action_name, 'nonce' );
		$result = $this->handle_action();
		wp_send_json( $result );
	}

	abstract function handle_action();
}