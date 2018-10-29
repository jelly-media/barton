<?php

abstract class Frizzly_Options {

	abstract public function get_name();

	abstract public function get_default();

	protected function sanitize( $input ) {
		return $input;
	}

	public function get() {
		$db_options = get_option( $this->get_name() );
		$db_options = $db_options != false ? $db_options : array();
		$defaults   = $this->get_default();
		$merged     = $this->merge_arrays( $defaults, $db_options );
		return $this->sanitize( $merged );
	}

	public function update( $new_value ) {
		$defaults = $this->get_default();
		$merged   = $this->merge_arrays( $defaults, $new_value );
		$merged = $this->sanitize( $merged );
		update_option( $this->get_name(), $merged );

		return $merged;
	}

	/**
	 * @param $defaults
	 * @param $db_options
	 *
	 * @return array
	 */
	private function merge_arrays( $defaults, $db_options ) {
		$merged = array();
		foreach ( $defaults as $key => $values ) {
			$merged[ $key ] = array_merge( $values, isset( $db_options[ $key ] ) ? $db_options[ $key ] : array() );
		}
		return $merged;
	}
}