<?php

abstract class Frizzly_Client_Submodule {

	public $slug;
	/**
	 * @var Frizzly_Options
	 */
	private $option;

	function __construct( $slug, $option ) {
		$this->slug = $slug;
		$this->option = $option;
	}

	function get_i18n() {
		return array();
	}

	function get_module_options() {
		return $this->option->get();
	}

	function get_submodule_options() {
		$options = $this->get_module_options();
		return isset( $options[ $this->slug ] ) ? $options[ $this->slug ] : array();
	}

	/**
	 * @return boolean
	 */
	function is_active() {
		$module_options = $this->get_module_options();
		return isset( $module_options[ 'general' ] ['active_' . $this->slug ] ) &&
		       $module_options[ 'general' ] ['active_' . $this->slug ];
	}
}