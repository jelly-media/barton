<?php

abstract class Frizzly_Admin_Submodule {
	public $name;
	public $slug;

	function __construct( $slug, $name ) {
		$this->name   = $name;
		$this->slug   = $slug;
	}

	function get_page_i18n() {
		return array();
	}

	abstract function get_page_settings( $db_value );

	function is_current_tab() {
		return isset( $_GET['tab'] ) && $this->slug === $_GET['tab'];
	}

	function is_current_tab_or_empty() {
		return $this->is_current_tab() || ! isset( $_GET['tab'] );
	}

	abstract function show_notice( $is_current_settings_screen, $options );

	/**
	 * @param $current_value
	 * @param $default
	 *
	 * @return Frizzly_Validator
	 */
	function validate( $current_value, $default ) {
		return new Frizzly_Validator( $current_value, $default, $this->get_page_settings( $default ) );
	}

	/**
	 * @param $args array
	 *
	 * @return array
	 */
	protected function create_image_selector( $args ) {
		$base_args = array(
			'button_text' => __( 'Select image', 'frizzly' ),
			'title_text'  => __( 'Choose image', 'frizzly' )
		);

		return array_merge( $base_args, $args );
	}
}