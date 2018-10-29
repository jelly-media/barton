<?php

class Frizzly_Client_Facebook_Meta_Tags {

	private $network_name;

	function __construct() {
		$this->network_name = 'facebook';
	}

	function print_tags( $post_id ) {
		$provider = new Frizzly_Social_Data_Provider( $post_id );
		$elements = new Frizzly_Meta_Elements();
		$elements
			->add_element( 'og:title', $provider->get_title( $this->network_name ) )
			->add_element( 'og:type', 'blog' )
			->add_element( 'og:url', $provider->get_url() )
			->add_element( 'og:site_name', $provider->get_site_name( $this->network_name ) )
			->add_element( 'og:description', $provider->get_description( $this->network_name ) );
		$img = $provider->get_image_url( $this->network_name );
		if ( false !== $img ) {
			$elements->add_element( 'og:image', $img );
		}
		echo $elements->get_html();
	}
}