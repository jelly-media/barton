<?php

class Frizzly_Admin_Notice {
	private $type;
	private $is_dismissible;
	private $message;


	/**
	 * Frizzly_Admin_Notice constructor.
	 *
	 * @param $type string
	 * @param $is_dismissible boolean
	 * @param $message string
	 */
	public function __construct($type, $is_dismissible, $message) {
		$this->type = $type;
		$this->is_dismissible = $is_dismissible;
		$this->message = $message;
	}

	function get_html() {
		$class = sprintf( 'notice%1$s%2$s',
			$this->is_dismissible ? ' is-dismissible' : '',
			' notice-' . $this->type
		);
		return sprintf('<div class="%s"><p>%s</p></div>', $class, $this->message);
	}



}