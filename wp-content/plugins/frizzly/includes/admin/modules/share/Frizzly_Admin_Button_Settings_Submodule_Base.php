<?php

class Frizzly_Admin_Button_Settings_Submodule_Base extends Frizzly_Admin_Submodule {

	function get_page_settings( $db_value ) {
		$settings = array();

		$settings['networks'] = array(
			'key'              => 'networks',
			'label'            => __( 'Networks', 'frizzly' ),
			'options'          => array(
				'digg'        => 'Digg',
				'email'       => __( 'Email', 'frizzly' ),
				'facebook'    => 'Facebook',
				'googleplus'  => 'Google+',
				'linkedin'    => 'LinkedIn',
				'pinterest'   => 'Pinterest',
				'reddit'      => 'Reddit',
				'stumbleupon' => 'StumbleUpon',
				'twitter'     => 'Twitter',
			),
			'type'             => 'multiselect',
			'min'              => 1,
			'error_messages'   => array(
				'min' => __( 'You need to select at least one network.', 'frizzly' )
			),
			'description'      => __( 'Networks that will be available to share on.', 'frizzly' ),
			'modalDescription' => __( 'Select and order networks that you want to use. You can order networks by dragging and dropping them.', 'frizzly' ),
			'selectedText'     => __( 'Selected networks', 'frizzly' ),
			'availableText'    => __( 'Available networks', 'frizzly' ),
			'modalConfirm'     => __( 'Confirm', 'frizzly' ),
			'modalTitle'       => __( 'Select networks', 'frizzly' )
		);

		$settings['button_size'] = array(
			'key'     => 'button_size',
			'options' => array(
				'xsmall' => __( 'very small', 'frizzly' ),
				'small'  => __( 'small', 'frizzly' ),
				'normal' => __( 'normal', 'frizzly' ),
				'large'  => __( 'large', 'frizzly' ),
				'xlarge' => __( 'very large', 'frizzly' )
			),
			'type'    => 'select',
		);

		$settings['button_shape'] = array(
			'key'     => 'button_shape',
			'options' => array(
				'square'            => __( 'square', 'frizzly' ),
				'rounded'           => __( 'rounded', 'frizzly' ),
				'round'             => __( 'round', 'frizzly' ),
				'rectangle'         => __( 'rectangle', 'frizzly' ),
				'rounded-rectangle' => __( 'rounded rectangle', 'frizzly' ),
			),
			'type'    => 'select'
		);

		$settings['enabled_on'] = array(
			'key'   => 'enabled_on',
			'label' => __( 'Enabled on', 'frizzly' ),
			'type'  => 'text',
		);

		$settings['disabled_on'] = array(
			'key'   => 'disabled_on',
			'label' => __( 'Disabled on', 'frizzly' ),
			'type'  => 'text'
		);

		return $settings;
	}

	function get_page_i18n() {
		return array(
			'where_title'       => __( 'On which pages should the buttons be shown', 'frizzly' ),
			'where_description' => __( 'Separate tags using commas. For the button to show up on a certain page, the page must be included in the "Enabled on" section and not included in the "Disabled on" section. You can use the following tags:' )
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
	}

	function show_notice( $is_current_settings_screen, $options ) {
		$return_condition = ! $is_current_settings_screen ||
		                    ! $this->is_current_tab() ||
		                    $options['general'][ 'active_' . $this->slug ];
		if ( $return_condition ) {
			return;
		}

		$notice = new Frizzly_Admin_Notice( 'warning', true,
			sprintf(
				__( 'This module is not active. Changing anything won\'t affect the website. <a href="%s" class="button button-primary" style="margin-left: 10px">Go to General settings to activate it &rarr;</a>', 'frizzly' ),
				admin_url( 'options-general.php?page=frizzly_settings&tab=general' )
			) );
		echo $notice->get_html();
	}
}