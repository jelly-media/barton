<?php

class Frizzly_Client_Content_Submodule extends Frizzly_Client_Submodule {

	function __construct( $option ) {
		parent::__construct( 'content', $option );
		if ( $this->is_active() ) {
			$this->add_filters();
		}
	}

	private function add_filters() {
		$content_filters = array( 'the_content', 'the_excerpt', 'post_thumbnail_html' );
		foreach ( $content_filters as $filter ) {
			add_filter( $filter, array( $this, 'filter_add_pinterest_data' ) );
		}
		add_filter( 'the_content', array( $this, 'filter_add_share_icons' ), 10 );
	}

	private function add_to_content() {
		$options = $this->get_submodule_options();
		$before  = in_array( $options['where'], array( 'before', 'before_after' ) );
		$after   = in_array( $options['where'], array( 'after', 'before_after' ) );

		return ! is_feed() &&
		       Frizzly_Should_Run::should_execute( $options['enabled_on'], $options['disabled_on'] ) &&
		       ( $before || $after );
	}

	function filter_add_pinterest_data( $content ) {
		if ( ! $this->add_to_content() ) {
			return $content;
		}
		$post_id          = get_the_ID();
		$module_options   = $this->get_module_options();
		$pinterest_source = $module_options['general']['pinterest_source'];
		$options          = $this->get_submodule_options();
		$add_pinterest    = in_array( 'pinterest', $options['networks'] );
		$data_provider    = new Frizzly_Social_Data_Provider( $post_id );
		$atts_to_save     = array( 'src', 'alt', 'title' );
		preg_match_all( Frizzly_Consts::$html_img_pattern, $content, $images, PREG_SET_ORDER );

		foreach ( $images as $img ) {

			preg_match_all( Frizzly_Consts::$html_attr_pattern, $img[0], $attributes, PREG_SET_ORDER );

			$newImg     = '<img';
			$atts_saved = array();

			foreach ( $attributes as $att ) {
				if ( $add_pinterest && in_array( $att[1], $atts_to_save ) ) {
					$atts_saved[ $att[1] ] = $att[3];
				}
				$newImg .= $att[0];
			}

			if ( $add_pinterest ) {
				$a_data = array(
					'source' => $pinterest_source,
					'image'  => array(
						'url'         => isset( $atts_saved['src'] ) ? $atts_saved['src'] : '',
						'image_title' => isset( $atts_saved['title'] ) ? $atts_saved['title'] : '',
						'image_alt'   => isset( $atts_saved['alt'] ) ? $atts_saved['alt'] : ''
					)
				);
				$link   = Frizzly_Link_Generator::generate( 'pinterest', $data_provider, $a_data );
				if ( strlen( $link ) > 0 ) {
					$newImg .= sprintf( ' data-frizzly-content-share-pinterest="%s"', esc_attr( $link ) );
				}
			}

			$newImg .= sprintf( 'data-frizzly-content-post-id="%s">', $post_id );
			$content = str_replace( $img[0], $newImg, $content );
		}

		return $content;
	}

	/**
	 * @param $content string
	 *
	 * @return string
	 */
	function filter_add_share_icons( $content ) {
		if ( ! $this->add_to_content() ) {
			return $content;
		}
		$options   = $this->get_submodule_options();
		$before    = in_array( $options['where'], array( 'before', 'before_after' ) );
		$after     = in_array( $options['where'], array( 'after', 'before_after' ) );
		$generator = new Frizzly_Button_Generator( $options );
		$html      = $generator->get_html( get_the_ID() );

		return sprintf( '%s%s%s', $before ? $html : '', $content, $after ? $html : '' );
	}

	function get_i18n() {
		return array(
			'pinmarklet' => array(
				'choose'    => __( 'Choose an image to Pin', 'frizzly' ),
				'no_images' => __( 'There are no images to share in this post', 'frizzly' )
			)
		);
	}
}