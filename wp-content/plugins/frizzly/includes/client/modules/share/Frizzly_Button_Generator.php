<?php

class Frizzly_Button_Generator {
	private $options;

	function __construct( $options = array() ) {
		$this->options = $options;
	}

	function get_html( $post_id ) {
		$data_provider = new Frizzly_Social_Data_Provider( $post_id );
		$div_classes   = array(
			'frizzly-content',
			'frizzly-button-container',
			'frizzly-button-size-' . $this->options['button_size'],
			'frizzly-theme-' . $this->options['button_shape'],
			$this->get_align_class()
		);
		$container     = new Frizzly_Html_Element( 'div' );
		$container->add_attribute( 'class', join( ' ', $div_classes ) );
		foreach ( $this->options['networks'] as $network_name ) {
			$link            = Frizzly_Link_Generator::generate( $network_name, $data_provider );
			$additional_data = $data_provider->get_additional_data( $network_name );
			$anchor          = new Frizzly_Html_Element( 'a' );
			$anchor
				->add_attributes( array(
					'target' => $this->get_target_attribute( $network_name ),
					'class'  => 'frizzly-button frizzly-' . $network_name,
					'href'   => $link
				) )
				->add_attributes( $this->get_attributes( $network_name, $post_id, $additional_data ) );

			$icon = new Frizzly_Html_Element( 'i' );
			$icon->add_attribute( 'class', 'fa fa-fw ' . $this->get_network_class( $network_name ) );

			$anchor->append_element( $icon );
			$container->append_element( $anchor );
		}

		return $container->get_html();
	}

	function get_attributes( $network, $post_id, $additional_data ) {
		switch ( $network ) {
			case 'pinterest':
				if ( ! isset( $additional_data['image'] ) || false === $additional_data['image'] ) {
					return array( 'data-frizzly-pinmarklet' => $post_id );
				}

				return array();
			case 'email':
				return array( 'data-frizzly-post-id' => $post_id );
			default:
				return array();
		}
	}

	function get_network_class( $network ) {
		switch ( $network ) {
			case 'email':
				return 'fa-envelope-o';
			case 'googleplus':
				return 'fa-google-plus';
			default:
				return 'fa-' . $network;
		}
	}

	private function get_target_attribute( $network ) {
		switch ( $network ) {
			case 'email':
				return '_self';
			default:
				return '_blank';
		}
	}

	private function get_align_class() {
		$align = array_key_exists( 'align', $this->options ) ? $this->options['align'] : '';
		switch ( $align ) {
			case 'center':
			case 'left':
			case 'right':
				return 'frizzly-' . $align;
			default:
				return '';
		}
	}
}
