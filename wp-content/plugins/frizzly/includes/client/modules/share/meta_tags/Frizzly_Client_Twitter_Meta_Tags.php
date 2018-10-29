<?php

class Frizzly_Client_Twitter_Meta_Tags {

	private $network_name;

	function __construct() {
		$this->network_name = 'twitter';
	}

	function print_tags( $post_id, $options ) {
		$provider = new Frizzly_Social_Data_Provider( $post_id );
		$elements = new Frizzly_Meta_Elements();
		$elements
			->add_element( 'twitter:card', $options['meta_twitter_card_type'])
			->add_element( 'twitter:site', $options['twitter_handle'])
			->add_element( 'twitter:description', $provider->get_description( $this->network_name ) )
			->add_element( 'twitter:title', $provider->get_title( $this->network_name ) );
		$img = $provider->get_image_url( $this->network_name );
		if ( false !== $img ) {
			$elements->add_element( 'twitter:image', $img );
		}
		echo $elements->get_html();
	}
}