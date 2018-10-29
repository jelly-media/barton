<?php

abstract class Frizzly_Meta {

	private $meta_key;

	function __construct( $meta_key ) {
		$this->meta_key = $meta_key;
	}

	abstract protected function get_defaults();

	function get( $post_id ) {
		$def  = $this->get_defaults();
		$meta = get_post_meta( $post_id, $this->meta_key, true );

		return $this->merge_arrays( $def, $meta );
	}

	function get_key() {
		return $this->meta_key;
	}

	private function merge_arrays( $defaults, $db_options ) {
		$merged = array();
		foreach ( $defaults as $key => $values ) {
			$merged[ $key ] = is_array( $values )
				? ( array_merge( $values, isset( $db_options[ $key ] ) ? $db_options[ $key ] : array() ) )
				: ( isset( $db_options[ $key ] ) ? $db_options[ $key ] : $values );
		}

		return $merged;
	}

	function update( $post_id, $new_value ) {
		$defaults      = $this->get_defaults();
		$updated_value = $this->merge_arrays( $defaults, $new_value );
		update_post_meta( $post_id, $this->meta_key, $updated_value );
	}

}