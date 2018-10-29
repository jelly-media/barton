<?php

class Frizzly_Client_Image_Submodule extends Frizzly_Client_Submodule {

	function __construct( $option ) {
		parent::__construct( 'image', $option );

		if ( ! $this->is_active() ) {
			return;
		}
		$content_filters = array( 'the_content', 'the_excerpt', 'post_thumbnail_html' );
		foreach ( $content_filters as $filter ) {
			add_filter( $filter, array( $this, 'filter_the_content' ) );
		}
	}

	/**
	 * @param $content string
	 *
	 * @return string
	 */
	function filter_the_content( $content ) {
		$options           = $this->get_submodule_options();
		$return_conditions = is_feed() ||
		                     ! Frizzly_Should_Run::should_execute( $options['enabled_on'], $options['disabled_on'] );
		if ( $return_conditions ) {
			return $content;
		}
		$content = $this->add_image_attributes( $content, get_the_ID() );

		return '<input type="hidden" class="frizzly">' . $content;
	}

	/**
	 * Adds necessary attributes to images.
	 * This piece of code uses a lot of code from the Photo Protect http://wordpress.org/plugins/photo-protect/ plugin
	 *
	 * @param $content string
	 * @param $post_id int
	 *
	 * @return mixed
	 */
	private function add_image_attributes( $content, $post_id ) {
		$module_options   = $this->get_module_options();
		$pinterest_source = $module_options['general']['pinterest_source'];
		$options          = $this->get_submodule_options();
		$networks         = $options['networks'];
		$data_provider    = new Frizzly_Social_Data_Provider( $post_id );
		$atts_to_save     = array( 'alt', 'title', 'src' );
		preg_match_all( Frizzly_Consts::$html_img_pattern, $content, $images, PREG_SET_ORDER );

		foreach ( $images as $img ) {
			preg_match_all( Frizzly_Consts::$html_attr_pattern, $img[0], $attributes, PREG_SET_ORDER );
			$newImg     = '<img';
			$atts_saved = array();

			foreach ( $attributes as $att ) {
				if ( in_array( $att[1], $atts_to_save ) ) {
					$atts_saved[ $att[1] ] = $att[3];
				}
				$newImg .= $att[0];
			}

			foreach ( $networks as $network_name ) {
				$a_data = 'pinterest' == $network_name
					? array(
						'source' => $pinterest_source,
						'image'  => array(
							'url'         => isset( $atts_saved['src'] ) ? $atts_saved['src'] : '',
							'image_title' => isset( $atts_saved['title'] ) ? $atts_saved['title'] : '',
							'image_alt'   => isset( $atts_saved['alt'] ) ? $atts_saved['alt'] : ''
						)
					)
					: null;
				$link   = Frizzly_Link_Generator::generate( $network_name, $data_provider, $a_data );
				if ( strlen( $link ) > 0 ) {
					$newImg .= sprintf( ' data-frizzly-image-share-%s="%s"', $network_name, esc_attr( $link ) );
				}
			}
			$newImg .= sprintf( 'data-frizzly-image-post-id="%s">', $post_id );
			$content = str_replace( $img[0], $newImg, $content );
		}

		return $content;
	}

	/**
	 * @param $class_attribute string
	 *
	 * function gets the id of the image by searching for class with wp-image- prefix, otherwise returns empty string
	 *
	 * @return string
	 */
	function get_attachment_id_from_image_classes( $class_attribute ) {
		$classes = preg_split( '/\s+/', $class_attribute, - 1, PREG_SPLIT_NO_EMPTY );
		$prefix  = 'wp-image-';

		for ( $i = 0; $i < count( $classes ); $i ++ ) {

			if ( $prefix === substr( $classes[ $i ], 0, strlen( $prefix ) ) ) {
				return str_replace( $prefix, '', $classes[ $i ] );
			}
		}

		return '';
	}

	function get_attachment( $id, $src ) {
		$result = is_numeric( $id ) ? get_post( $id ) : null;

		if ( null === $result ) {
			$id     = $this->fjarrett_get_attachment_id_by_url( $src );
			$result = is_numeric( $id ) ? get_post( $id ) : null;
		}

		return $result;
	}

	/**
	 * Function copied from http://frankiejarrett.com/get-an-attachment-id-by-url-in-wordpress/
	 * Return an ID of an attachment by searching the database with the file URL.
	 *
	 * First checks to see if the $url is pointing to a file that exists in
	 * the wp-content directory. If so, then we search the database for a
	 * partial match consisting of the remaining path AFTER the wp-content
	 * directory. Finally, if a match is found the attachment ID will be
	 * returned.
	 *
	 * @return {int} $attachment
	 */
	function fjarrett_get_attachment_id_by_url( $url ) {

		// Split the $url into two parts with the wp-content directory as the separator.
		$parse_url = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );

		// Get the host of the current site and the host of the $url, ignoring www.
		$this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
		$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );

		// Return nothing if there aren't any $url parts or if the current host and $url host do not match.
		if ( ! isset( $parse_url[1] ) || empty( $parse_url[1] ) || ( $this_host != $file_host ) ) {
			return null;
		}

		// Now we're going to quickly search the DB for any attachment GUID with a partial path match.
		// Example: /uploads/2013/05/test-image.jpg
		global $wpdb;

		$prefix     = $wpdb->prefix;
		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM " . $prefix . "posts WHERE guid RLIKE %s;", $parse_url[1] ) );

		// Returns null if no attachment is found.
		return $attachment ? $attachment[0] : null;
	}
}