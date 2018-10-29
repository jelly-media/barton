<?php
/*
Plugin Name: WonderPlugin Tabs
Plugin URI: http://www.wonderplugin.com
Description: WordPress Tabs Plugin
Version: 5.9
Author: Magic Hills Pty Ltd
Author URI: http://www.wonderplugin.com
License: Copyright 2015 Magic Hills Pty Ltd, All Rights Reserved
*/

define('WONDERPLUGIN_TABS_VERSION', '5.9');
define('WONDERPLUGIN_TABS_URL', plugin_dir_url( __FILE__ ));
define('WONDERPLUGIN_TABS_PATH', plugin_dir_path( __FILE__ ));
define('WONDERPLUGIN_TABS_PLUGIN', basename(dirname(__FILE__)) . '/' . basename(__FILE__));
define('WONDERPLUGIN_TABS_PLUGIN_VERSION', '5.9');

require_once 'app/class-wonderplugin-tabs-controller.php';

class WonderPlugin_Tabs_Plugin {
	
	function __construct() {
	
		$this->init();
	}
	
	public function init() {
		
		// init controller
		$this->wonderplugin_tabs_controller = new WonderPlugin_Tabs_Controller();
		
		add_action( 'admin_menu', array($this, 'register_menu') );
		
		add_shortcode( 'wonderplugin_tabs', array($this, 'shortcode_handler') );
		add_shortcode( 'wonderplugin_tab_page', array($this, 'shortcode_handler_page') );
		
		add_action( 'init', array($this, 'register_script') );
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_script') );
		
		if ( is_admin() )
		{
			add_action( 'wp_ajax_wonderplugin_tabs_save_config', array($this, 'wp_ajax_save_item') );
			add_action( 'admin_init', array($this, 'admin_init_hook') );
			add_action( 'admin_post_wonderplugin_tabs_export', array($this, 'export_tabs') );
			
			if ( get_option( 'wonderplugin_tabs_supportajaxloading', 0 ) == 1)
			{
				add_action( 'wp_ajax_wonderplugin_tabs_get_tab_content', array($this, 'wp_ajax_get_tab_content') );
				add_action( 'wp_ajax_nopriv_wonderplugin_tabs_get_tab_content', array($this, 'wp_ajax_get_tab_content') );
			}
		}
		
		$supportwidget = get_option( 'wonderplugin_tabs_supportwidget', 1 );
		if ( $supportwidget == 1 )
		{
			add_filter('widget_text', 'do_shortcode');
		}
	}
	
	function register_menu()
	{
		
		$settings = $this->get_settings();
		$userrole = $settings['userrole'];
		
		$menu = add_menu_page(
				__('WonderPlugin Tabs', 'wonderplugin_tabs'),
				__('WonderPlugin Tabs', 'wonderplugin_tabs'),
				$userrole,
				'wonderplugin_tabs_overview',
				array($this, 'show_overview'),
				WONDERPLUGIN_TABS_URL . 'images/logo-16.png' );
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_tabs_overview',
				__('Overview', 'wonderplugin_tabs'),
				__('Overview', 'wonderplugin_tabs'),
				$userrole,
				'wonderplugin_tabs_overview',
				array($this, 'show_overview' ));
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_tabs_overview',
				__('New Tab Group', 'wonderplugin_tabs'),
				__('New Tab Group', 'wonderplugin_tabs'),
				$userrole,
				'wonderplugin_tabs_add_new',
				array($this, 'add_new' ));
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_tabs_overview',
				__('Manage Tab Groups', 'wonderplugin_tabs'),
				__('Manage Tab Groups', 'wonderplugin_tabs'),
				$userrole,
				'wonderplugin_tabs_show_items',
				array($this, 'show_items' ));
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_tabs_overview',
				__('Import/Export', 'wonderplugin_tabs'),
				__('Import/Export', 'wonderplugin_tabs'),
				'manage_options',
				'wonderplugin_tabs_import_export',
				array($this, 'import_export' ) );
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_tabs_overview',
				__('Settings', 'wonderplugin_tabs'),
				__('Settings', 'wonderplugin_tabs'),
				'manage_options',
				'wonderplugin_tabs_edit_settings',
				array($this, 'edit_settings' ) );
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		
		$menu = add_submenu_page(
				null,
				__('View Tab Group', 'wonderplugin_tabs'),
				__('View Tab Group', 'wonderplugin_tabs'),	
				$userrole,	
				'wonderplugin_tabs_show_item',	
				array($this, 'show_item' ));
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				null,
				__('Edit Tab Group', 'wonderplugin_tabs'),
				__('Edit Tab Group', 'wonderplugin_tabs'),
				$userrole,
				'wonderplugin_tabs_edit_item',
				array($this, 'edit_item' ) );
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
	}
	
	function register_script()
	{		
		wp_register_style('wonderplugin-tabs-admin-css', WONDERPLUGIN_TABS_URL . 'wonderplugintabs.css');
		wp_register_style('wonderplugin-tabs-engine-css', WONDERPLUGIN_TABS_URL . 'engine/wonderplugin-tabs-engine.css');
		wp_register_script('wonderplugin-tabs-engine-script', WONDERPLUGIN_TABS_URL . 'engine/wonderplugin-tabs-engine.js', array('jquery'), WONDERPLUGIN_TABS_VERSION, false);
		wp_register_script('wonderplugin-tabs-creator-script', WONDERPLUGIN_TABS_URL . 'app/wonderplugin-tabs-creator.js', array('jquery', 'wp-color-picker'), WONDERPLUGIN_TABS_VERSION, false);	
		wp_register_script('wonderplugin-tabs-skins-script', WONDERPLUGIN_TABS_URL . 'app/wonderplugin-tabs-skins.js', array('jquery'), WONDERPLUGIN_TABS_VERSION, false);
		wp_register_style('font-awesome', WONDERPLUGIN_TABS_URL . 'font-awesome/css/font-awesome.min.css');	
		
		wp_register_script( 'js-wp-editor', WONDERPLUGIN_TABS_URL . 'app/js-wp-editor.js', array('jquery'), WONDERPLUGIN_TABS_VERSION, false);
		wp_localize_script( 'js-wp-editor', 'ap_vars', array(
				'url' => get_home_url(),
				'includes_url' => includes_url()
		));
	}
	
	function enqueue_script()
	{
		$loadfontawesome = get_option( 'wonderplugin_tabs_loadfontawesome', 1 );
		if ($loadfontawesome == 1)
			wp_enqueue_style('font-awesome');
		
		wp_enqueue_style('wonderplugin-tabs-engine-css');
		
		$addjstofooter = get_option( 'wonderplugin_tabs_addjstofooter', 0 );
		if ($addjstofooter == 1)
		{
			wp_enqueue_script('wonderplugin-tabs-engine-script', false, array(), false, true);
		}
		else
		{
			wp_enqueue_script('wonderplugin-tabs-engine-script');
		}

		if ( get_option( 'wonderplugin_tabs_supportajaxloading', 0 ) == 1)
		{
			wp_localize_script('wonderplugin-tabs-engine-script', 'wonderplugintabs_ajaxobject', array( 'ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('wonderplugin-tabs-ajaxnonce') ));
		}
	}
	
	function enqueue_admin_script($hook)
	{
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_style ('wp-jquery-ui-dialog');
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_style('font-awesome');
		wp_enqueue_script('post');
		if (function_exists("wp_enqueue_media"))
		{
			wp_enqueue_media();
		}
		else
		{
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');
			wp_enqueue_script('media-upload');
		}
		wp_enqueue_script('wonderplugin-tabs-engine-script');
		wp_enqueue_style('wonderplugin-tabs-engine-css');
		wp_enqueue_style('wonderplugin-tabs-admin-css');
		wp_enqueue_script('wonderplugin-tabs-creator-script');
		wp_enqueue_script('wonderplugin-tabs-skins-script');
		wp_enqueue_script( 'js-wp-editor' );
		
		if ( get_option( 'wonderplugin_tabs_supportajaxloading', 0 ) == 1)
		{
			wp_localize_script('wonderplugin-tabs-engine-script', 'wonderplugintabs_ajaxobject', array( 'ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('wonderplugin-tabs-ajaxnonce') ));
		}
	}

	function admin_init_hook()
	{
		$settings = $this->get_settings();
		$userrole = $settings['userrole'];
		
		if ( !current_user_can($userrole) )
			return;
		
		// change text of history media uploader
		if (!function_exists("wp_enqueue_media"))
		{
			global $pagenow;
			
			if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {
				add_filter( 'gettext', array($this, 'replace_thickbox_text' ), 1, 3 );
			}
		}
		
		// add meta boxes
		$this->wonderplugin_tabs_controller->add_metaboxes();
		
		$tinymceeditor = get_option( 'wonderplugin_tabs_tinymceeditor', 0 );
		if ( $tinymceeditor == 1 )
			$this->init_js_wp_editor();
	}
	
	function init_js_wp_editor()
	{
		if ( ! class_exists( '_WP_Editors' ) )
			require( ABSPATH . WPINC . '/class-wp-editor.php' );
	
		$set = _WP_Editors::parse_settings( 'apid', array() );
		if ( !current_user_can( 'upload_files' ) )
			$set['media_buttons'] = false;
			
		_WP_Editors::editor_settings( 'apid', $set );
	}
	
	function replace_thickbox_text($translated_text, $text, $domain) {
		
		if ('Insert into Post' == $text) {
			$referer = strpos( wp_get_referer(), 'wonderplugin-tabs' );
			if ( $referer != '' ) {
				return __('Insert into tabs', 'wonderplugin_tabs' );
			}
		}
		return $translated_text;
	}
	
	function show_overview() {
		
		$this->wonderplugin_tabs_controller->show_overview();
	}
	
	function show_items() {
		
		$this->wonderplugin_tabs_controller->show_items();
	}
	
	function add_new() {
		
		$this->wonderplugin_tabs_controller->add_new();
	}
	
	function show_item() {
		
		$this->wonderplugin_tabs_controller->show_item();
	}
	
	function edit_item() {
	
		$this->wonderplugin_tabs_controller->edit_item();
	}
	
	function edit_settings() {
	
		$this->wonderplugin_tabs_controller->edit_settings();
	}
	
	function get_settings() {
	
		return $this->wonderplugin_tabs_controller->get_settings();
	}
	
	function register() {
	
		$this->wonderplugin_tabs_controller->register();
	}
	
	function shortcode_handler($atts, $content = null) {
		
		if ( !isset($atts['id']) && !isset($atts['name']))
			return __('Please specify a tabs id or name', 'wonderplugin_tabs');
				
		$inline_content = (isset($atts['inline']) && ($atts['inline'] == '1')) ?  $content : null;
		
		$attributes = array();
		foreach($atts as $key => $value)
		{
			$key = strtolower($key);
			if (strlen($key) > 5 && substr($key, 0, 5) === 'data-')
				$attributes[substr($key, 5)] = $value;
		}
		
		return $this->wonderplugin_tabs_controller->generate_body_code( (isset($atts['id']) ? $atts['id'] : null), (isset($atts['name']) ? $atts['name'] : null), $inline_content, $attributes, false);
	}
	
	function shortcode_handler_page($atts, $content = null) {
		
		if ( !isset($atts['id']) )
			return __('Please specify a page id', 'wonderplugin_tabs');
		
		$autop = ( isset($atts['wpautop']) && $atts['wpautop'] == '0') ? false : true;
		
		return $this->wonderplugin_tabs_controller->get_page_code( $atts['id'], $autop );
	}
	
	function wp_ajax_get_tab_content() {
		
		if ( get_option( 'wonderplugin_tabs_ajaxverifynonce', 0 ) == 1 )
			check_ajax_referer('wonderplugin-tabs-ajaxnonce', 'nonce');
		
		header('Content-Type: application/json');
		echo json_encode($this->wonderplugin_tabs_controller->get_tab_content($_POST));
		wp_die();
	}
	
	function wp_ajax_save_item() {
				
		check_ajax_referer( 'wonderplugin-tabs-ajaxnonce', 'nonce' );
		
		$settings = $this->get_settings();
		$userrole = $settings['userrole'];
		
		if ( !current_user_can($userrole) )
			return;
		
		$jsonstripcslash = get_option( 'wonderplugin_tabs_jsonstripcslash', 1 );
		if ($jsonstripcslash == 1)
			$json_post = trim(stripcslashes($_POST["item"]));
		else
			$json_post = trim($_POST["item"]);
		
		$items = json_decode($json_post, true);
				
		if ( empty($items) )
		{
			$json_error = "json_decode error";
			if ( function_exists('json_last_error_msg') )
				$json_error .= ' - ' . json_last_error_msg();
			else if ( function_exists('json_last_error') )
				$json_error .= 'code - ' . json_last_error();
				
			header('Content-Type: application/json');
			echo json_encode(array(
					"success" => false,
					"id" => -1,
					"message" => $json_error
			));
			wp_die();
		}
		
		if (!current_user_can('manage_options'))
		{
			unset($items['customjs']);
		}
		
		add_filter('safe_style_css', 'wonderplugin_tabs_css_allow');
		add_filter('wp_kses_allowed_html', 'wonderplugin_tabs_tags_allow', 'post');
		foreach ($items as $key => &$value)
		{
			if ($key == 'customjs' && current_user_can('manage_options'))
				continue;
			
			if ($value === true)
				$value = "true";
			else if ($value === false)
				$value = "false";
			else if ( is_string($value) )
				$value = wp_kses_post($value);
		}
		
		if (isset($items["slides"]) && count($items["slides"]) > 0)
		{
			foreach ($items["slides"] as $key => &$slide)
			{
				foreach ($slide as $key => &$value)
				{
					if ($value === true)
						$value = "true";
					else if ($value === false)
						$value = "false";
				}
			}
		}
		remove_filter('wp_kses_allowed_html', 'wonderplugin_tabs_tags_allow', 'post');
		remove_filter('safe_style_css', 'wonderplugin_tabs_css_allow');
		
		header('Content-Type: application/json');
		echo json_encode($this->wonderplugin_tabs_controller->save_item($items));
		wp_die();
	}
	
	function import_export() {
	
		$this->wonderplugin_tabs_controller->import_export();
	}
	
	function export_tabs() {
	
		check_admin_referer('wonderplugin-tabs', 'wonderplugin-tabs-export');
	
		if ( !current_user_can('manage_options') )
			return;
	
		$this->wonderplugin_tabs_controller->export_tabs();
	}
}

/**
 * Init the plugin
 */
$wonderplugin_tabs_plugin = new WonderPlugin_Tabs_Plugin();

/**
 * Uninstallation
 */
function wonderplugin_tabs_uninstall() {

	if ( ! current_user_can( 'activate_plugins' ) )
		return;
	
	global $wpdb;
	
	$keepdata = get_option( 'wonderplugin_tabs_keepdata', 1 );
	if ( $keepdata == 0 )
	{
		$table_name = $wpdb->prefix . "wonderplugin_tabs";
		$wpdb->query("DROP TABLE IF EXISTS $table_name");
	}	

}

if ( function_exists('register_uninstall_hook') )
{
	register_uninstall_hook( __FILE__, 'wonderplugin_tabs_uninstall' );
}

define('WONDERPLUGIN_TABS_VERSION_TYPE', 'F');
