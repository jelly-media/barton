<?php

class Frizzly_Html_Element {

	private $tag;
	/**
	 * @var array
	 */
	private $attributes;
	/**
	 * @var Frizzly_Html_Element[]
	 */
	private $elements;

	/**
	 * Frizzly_Html_Element constructor.
	 *
	 * @param $tag string
	 */
	function __construct( $tag ) {
		$this->tag        = $tag;
		$this->attributes = array();
		$this->elements   = array();
	}

	function add_attribute( $name, $value ) {
		$this->attributes[ $name ] = $value;

		return $this;
	}

	function add_attributes( $atts ) {
		$this->attributes = array_merge( $this->attributes, $atts );

		return $this;
	}

	/**
	 * @param $elem Frizzly_Html_Element
	 */
	function append_element( $elem ) {
		$this->elements[] = $elem;

		return $this;
	}

	/**
	 * @return string
	 */
	function get_html() {
		$html = sprintf( '<%s', $this->tag );
		foreach ( $this->attributes as $att_name => $att_value ) {
			$html .= sprintf( ' %s="%s"', $att_name, esc_attr( $att_value ) );
		}
		$html .= '>';
		foreach ( $this->elements as $element ) {
			$html .= $element->get_html();
		}
		$html .= sprintf( '</%s>', $this->tag );

		return $html;
	}
}