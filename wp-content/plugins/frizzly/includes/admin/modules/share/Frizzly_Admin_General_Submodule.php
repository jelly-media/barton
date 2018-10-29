<?php

class Frizzly_Admin_General_Submodule extends Frizzly_Admin_Submodule {

	private $nag_no_active_modules;

	function __construct() {
		parent::__construct( 'general', __( 'General', 'frizzly' ) );
		$this->nag_no_active_modules = 'frizzly_nag_no_active_modules';

		add_action( 'admin_init', array( $this, 'hide_nag_no_active_modules' ) );
	}

	function hide_nag_no_active_modules() {
		if ( ! isset( $_GET[ $this->nag_no_active_modules ] ) ) {
			return;
		}
		global $current_user;
		add_user_meta( $current_user->ID, $this->nag_no_active_modules, '1', true );
		wp_redirect( remove_query_arg( $this->nag_no_active_modules ) );
		exit;
	}

	function get_page_i18n() {
		return array(
			'active_modules_title'      => __( 'Active modules', 'frizzly' ),
			'modules_title'             => __( 'Modules settings', 'frizzly' ),
			'image_module_href'         => admin_url( 'options-general.php?page=frizzly_settings&tab=image' ),
			'content_module_href'       => admin_url( 'options-general.php?page=frizzly_settings&tab=content' ),
			'module_configuration_link' => __( 'Configure &rarr;', 'frizzly' ),
			'networks_title'            => __( 'Networks settings', 'frizzly' ),
			'meta_title'                => __( 'Meta settings', 'frizzly' ),
			'meta_data_label'           => __( 'Meta data', 'frizzly' ),
			'meta_data_description'     => __( 'Meta data is a list of additional meta tags in your site\'s <code>&lt;head&gt;</code> section. This data is used by share networks when someone shares your content. Open Graph meta data is used by Facebook, Pinterest, and other networks. Twitter has its own set of meta tags. If you already have a plugin that adds meta data to your website, settings above should be disabled. You can read more about Twitter cards <a href="https://dev.twitter.com/cards/overview" target="_blank">here</a>.', 'frizzly' ),
			'meta_twitter_template'     => __( '%meta% <label for="meta_twitter_card_type">and set the default card type to</label> %card%', 'frizzly' ),
		);
	}

	function get_page_settings( $db_value ) {
		$settings = array();

		/* MODULES */
		$settings['active_image'] = array(
			'key'     => 'active_image',
			'text'    => __( 'Image', 'frizzly' ),
			'tooltip' => __( 'Image module shows share icons over images in your posts.', 'frizzly' ),
			'type'    => 'boolean'
		);

		$settings['active_content'] = array(
			'key'     => 'active_content',
			'text'    => __( 'Content', 'frizzly' ),
			'tooltip' => __( 'Content module shows share icons before, after, or before and after your posts.', 'frizzly' ),
			'type'    => 'boolean'
		);

		/* TWITTER */
		$settings['add_handle_to_tweets'] = array(
			'key'   => 'add_handle_to_tweets',
			'label' => __( 'Append "via @user" to tweets', 'frizzly' ),
			'type'  => 'boolean'
		);

		$settings['twitter_handle'] = array(
			'key'         => 'twitter_handle',
			'label'       => __( 'Twitter username', 'frizzly' ),
			'placeholder' => __( 'Your Twitter handle', 'frizzly' ),
			'description' => __( 'Twitter handle is used in tweets if you decide to append "via @user" to them using the setting below. It is also used in Twitter meta data configured below.', 'frizzly' ),
			'type'        => 'text'
		);

		/* PINTEREST */
		$settings['pinterest_source'] = array(
			'key'            => 'pinterest_source',
			'label'          => __( 'Pinterest description source', 'frizzly' ),
			'description'    => __( 'Select and prioritize sources you want to be used when populating Pin description. The plugin starts with the first source and finishes when it finds a non-empty value. For example, if you choose Image alt text and Post Title, when the alt tag is empty, the Post title becomes the description', 'frizzly' ),
			'options'        => array(
				'post_title'  => __( 'Post title', 'frizzly' ),
				'image_title' => __( 'Image title', 'frizzly' ),
				'image_alt'   => __( 'Image alt text', 'frizzly' )
			),
			'type'           => 'multiselect',
			'min'            => 1,
			'error_messages' => array(
				'min' => __( 'You need to select at least one Pinterest description source.', 'frizzly' )
			),
		);

		/* META */
		$settings['meta_open_graph'] = array(
			'key'  => 'meta_open_graph',
			'text' => __( 'Add Open Graph meta data', 'frizzly' ),
			'type' => 'boolean',
		);

		$settings['meta_twitter'] = array(
			'key'  => 'meta_twitter',
			'text' => __( 'Add Twitter meta data', 'frizzly' ),
			'type' => 'boolean',
		);

		$settings['meta_twitter_card_type'] = array(
			'key'     => 'meta_twitter_card_type',
			'options' => array(
				'summary'             => __( 'Summary', 'frizzly' ),
				'summary_large_image' => __( 'Summary with large image', 'frizzly' )
			),
			'type'    => 'select',
		);

		foreach ( $settings as $key => $setting ) {
			$settings[ $key ]['value'] = $db_value[ $key ];
		}

		return $settings;
	}

	function show_notice( $is_current_settings_screen, $share_options ) {
		$active_modules = $share_options['general']['active_image'] || $share_options['general']['active_content'];
		if ( $active_modules ) {
			return;
		}

		if ( $is_current_settings_screen && $this->is_current_tab_or_empty() ) {
			$notice = new Frizzly_Admin_Notice( 'error', true, __( 'There are no active modules. Activate at least one to get going.', 'frizzly' ) );
			echo $notice->get_html();
		} else {
			$this->show_nag_no_active_modules();
		}
	}

	private function show_nag_no_active_modules() {
		global $current_user;
		$meta = get_user_meta( $current_user->ID, $this->nag_no_active_modules, true );
		if ( '1' === $meta ) {
			return;
		}
		$notice = new Frizzly_Admin_Notice( 'error', true, sprintf(
			__( '<b>Frizzly</b> is almost ready! You need to activate the modules you want to use. <a class="button button-primary" style="margin-left: 10px" href="%s">Go to settings &rarr;</a> <a class="button button-secondary" href="%s">Don\'t bother me again</a>', 'frizzly' ),
			admin_url( 'options-general.php?page=frizzly_settings&tab=general' ),
			add_query_arg( $this->nag_no_active_modules, '1' )
		) );
		echo $notice->get_html();
	}
}