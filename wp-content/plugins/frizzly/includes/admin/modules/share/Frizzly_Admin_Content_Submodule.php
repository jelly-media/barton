<?php

class Frizzly_Admin_Content_Submodule extends Frizzly_Admin_Button_Settings_Submodule_Base {

	function __construct() {
		parent::__construct( 'content', __( 'Content', 'frizzly' ) );
	}

	function get_page_settings( $db_value ) {
		$settings = parent::get_page_settings( $db_value );

		$settings['align'] = array(
			'key'         => 'align',
			'label'       => __( 'Align', 'frizzly' ),
			'options'     => array(
				'left'   => __( 'aligned to the left', 'frizzly' ),
				'center' => __( 'centered', 'frizzly' ),
				'right'  => __( 'aligned to the right', 'frizzly' )
			),
			'type' => 'select',
		);

		$settings['where'] = array(
			'key'     => 'where',
			'label'   => __( 'Share buttons placement', 'frizzly' ),
			'options' => array(
				'before_after' => __( 'Before and after post', 'frizzly' ),
				'before'       => __( 'Before post', 'frizzly' ),
				'after'        => __( 'After post', 'frizzly' )
			)
		);

		$settings['pinterest_behavior'] = array(
			'key'         => 'pinterest_behavior',
			'label'       => __( 'Default Pinterest behavior', 'frizzly' ),
			'options'     => array(
				'user'     => __( 'Always allow user to choose image', 'frizzly' ),
				'featured' => __( 'Share featured image if available', 'frizzly' )
			),
			'description' => __( 'Choose how the Pinterest share button should work. If there is no featured image, the user will always have to choose the image they want to share.', 'frizzly' )
		);

		foreach ( $settings as $key => $setting ) {
			$settings[ $key ]['value'] = $db_value[ $key ];
		}

		return $settings;
	}

	function get_page_i18n() {
		$parent_i18n = parent::get_page_i18n();
		$new_i18n    = array(
			'module_description'    => __( 'Content module shows share icons inside (before, after or both) your posts.', 'frizzly' ),
			'visual_header'         => __( 'Visuals', 'frizzly' ),
			'selection_header'      => __( 'Selection', 'frizzly' ),
			'button_style_title'    => __( 'Button style', 'frizzly' ),
			'button_style_template' => __( 'Share buttons should be %size%, %shape% and %align%.', 'frizzly' ),
			'preview_title'         => __( 'Preview', 'frizzly' )
		);

		return array_merge( $parent_i18n, $new_i18n );
	}
}