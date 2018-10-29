<?php

class Frizzly_Admin_Image_Submodule extends Frizzly_Admin_Button_Settings_Submodule_Base {

	function __construct() {
		parent::__construct( 'image', __( 'Image', 'frizzly' ) );
	}

	function get_page_settings( $db_value ) {
		$settings = parent::get_page_settings( $db_value );

		$settings['show'] = array(
			'key'     => 'show',
			'options' => array(
				'hover' => __( 'on hover', 'frizzly' ),
//				'touch'  => __( 'always on touch devices, hover on desktop', 'frizzly' ),
//				'always' => __( 'always', 'frizzly' )
			),
			'type' => 'select'
		);

		$settings['button_position'] = array(
			'key'     => 'button_position',
			'label'   => __( 'Button position and margins', 'frizzly' ),
			'options' => array(
				'center'       => __( 'Center', 'frizzly' ),
				'top-left'     => __( 'Top left', 'frizzly' ),
				'top-right'    => __( 'Top right', 'frizzly' ),
				'bottom-left'  => __( 'Bottom left', 'frizzly' ),
				'bottom-right' => __( 'Bottom right', 'frizzly' )
			),
			'type' => 'select',
		);

		$margin_setting = array(
			'min' => 0,
			'step' => 1,
			'type' => 'int'
		);
		$settings['button_margin_top'] = array_merge( $margin_setting, array(
			'key' => 'button_margin_top',
			'error_label' => __( 'Top margin', 'frizzly' )
		) );

		$settings['button_margin_left'] = array_merge( $margin_setting, array(
			'key' => 'button_margin_left',
			'error_label' => __( 'Left margin', 'frizzly' )
		) );

		$settings['button_margin_right'] = array_merge( $margin_setting, array(
			'key' => 'button_margin_right',
			'error_label' => __( 'Right margin', 'frizzly' )
		) );

		$settings['button_margin_bottom'] = array_merge( $margin_setting, array(
			'key' => 'button_margin_bottom',
			'error_label' => __( 'Bottom margin', 'frizzly' )
		) );

		$settings['image_selector'] = array(
			'key'         => 'image_selector',
			'label'       => __( 'Image selector', 'frizzly' ),
			'description' => __( 'If you are familiar with jQuery, feel free to modify this selector to better fit your needs.', 'frizzly' ),
			'type' => 'text'
		);

		$resolution_settings = array(
			'min'  => 0,
			'step' => 1,
			'type' => 'int'
		);

		$settings['desktop_min_height'] = array_merge( array(
			'key'   => 'desktop_min_height',
			'label' => __( 'Height', 'frizzly' )
		), $resolution_settings );

		$settings['desktop_min_width'] = array_merge( array(
			'key'   => 'desktop_min_width',
			'label' => __( 'Width', 'frizzly' ),
		), $resolution_settings );

		$settings['image_classes_positive'] = array(
			'key'     => 'image_classes_positive',
			'truthy'  => true,
			'options' => array(
				true  => __( 'with', 'frizzly' ),
				false => __( 'without', 'frizzly' )
			),
			'type' => 'boolean'
		);

		$settings['image_classes'] = array(
			'key'         => 'image_classes',
			'description' => __( 'Classes should be separated by commas and without dots preceding names. If you leave the classes list empty, images will not be filtered at all.', 'frizzly' ),
			'type' => 'text',
		);

		foreach ( $settings as $key => $setting ) {
			$settings[ $key ]['value'] = $db_value[ $key ];
		}

		return $settings;
	}

	function get_page_i18n() {
		$parent_i18n = parent::get_page_i18n();
		$new_i18n    = array(
			'module_description'         => __( 'Image module shows share icons on your images.', 'frizzly' ),
			'visual_header'              => __( 'Visuals', 'frizzly' ),
			'selection_header'           => __( 'Image selection', 'frizzly' ),
			'button_style_title'         => __( 'Button style', 'frizzly' ),
			'button_style_template'      => __( 'Share buttons should be %size% and %shape%. They should be displayed %show%.', 'frizzly' ),
			'min_resolution_title'       => __( 'Minimum image resolution', 'frizzly' ),
			'min_resolution_template'    => __( 'Share buttons will show up only if the image is at least %height% pixels high and %width% pixels wide.', 'frizzly' ),
			'min_resolution_description' => __( 'This setting is checked against the size of image on screen, not the real size of the image.', 'frizzly' ),
			'image_classes_template'     => __( 'Select only images %discriminator% the following CSS classes: %classes%.', 'frizzly' ),
			'where_title'                => __( 'On which pages should the buttons be shown', 'frizzly' ),
			'where_description'          => __( 'Separate tags using commas. For the button to show up on a certain page, the page must be included in the "Enabled on" section and not included in the "Disabled on" section. You can use the following tags:' )
			                                . '<p>'
			                                . __( 'number (e.g. 588) - the ID of a certain page or post', 'frizzly' ) . '<br/>'
			                                . __( '[front] - front page', 'frizzly' ) . '<br/>'
			                                . __( '[single] - single posts', 'frizzly' ) . '<br/>'
			                                . __( '[page] - single pages', 'frizzly' ) . '<br/>'
			                                . __( '[archive] - archive pages', 'frizzly' ) . '<br/>'
			                                . __( '[search] - search pages', 'frizzly' ) . '<br/>'
			                                . __( '[category] - category pages', 'frizzly' ) . '<br/>'
			                                . __( '[home] - blog page', 'frizzly' )
			                                . '</p>'
			                                . __( 'Read more on <a href="https://codex.wordpress.org/Conditional_Tags" target="_blank">https://codex.wordpress.org/Conditional_Tags</a>.', 'frizzly' )
		);

		return array_merge( $parent_i18n, $new_i18n );
	}
}