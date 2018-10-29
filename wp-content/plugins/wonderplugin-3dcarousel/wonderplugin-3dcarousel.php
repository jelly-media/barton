<?php
/*
Plugin Name: WonderPlugin 3D Carousel
Plugin URI: http://www.wonderplugin.com
Description: WordPress 3D Image and Video Carousel Plugin
Version: 2.3
Author: Magic Hills Pty Ltd
Author URI: http://www.wonderplugin.com
License: Copyright 2017 Magic Hills Pty Ltd, All Rights Reserved
*/

define('WONDERPLUGIN_3DCAROUSEL_VERSION', '2.3');
define('WONDERPLUGIN_3DCAROUSEL_URL', plugin_dir_url( __FILE__ ));
define('WONDERPLUGIN_3DCAROUSEL_PATH', plugin_dir_path( __FILE__ ));
define('WONDERPLUGIN_3DCAROUSEL_PLUGIN', basename(dirname(__FILE__)) . '/' . basename(__FILE__));
define('WONDERPLUGIN_3DCAROUSEL_PLUGIN_VERSION', '2.3');

require_once 'app/class-wonderplugin-3dcarousel-controller.php';

class WonderPlugin_3DCarousel_Plugin {
	
	function __construct() {
	
		$this->init();
	}
	
	public function init() {
		
		// init controller
		$this->wonderplugin_3dcarousel_controller = new WonderPlugin_3DCarousel_Controller();
		
		add_action( 'admin_menu', array($this, 'register_menu') );
		
		add_shortcode( 'wonderplugin_3dcarousel', array($this, 'shortcode_handler') );
		
		add_action( 'init', array($this, 'register_script') );
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_script') );
		
		if ( is_admin() )
		{
			add_action( 'wp_ajax_wonderplugin_3dcarousel_save_config', array($this, 'wp_ajax_save_item') );
			add_action( 'admin_init', array($this, 'admin_init_hook') );
			add_action( 'admin_post_wonderplugin_3dcarousel_export', array($this, 'export_3dcarousel') );
		}
		
		$supportwidget = get_option( 'wonderplugin_3dcarousel_supportwidget', 1 );
		if ( $supportwidget == 1 )
		{
			add_filter('widget_text', 'do_shortcode');
		}
		
		$jetpackdisablelazy = get_option( 'wonderplugin_3dcarousel_jetpackdisablelazyload', 1 );
		if ($jetpackdisablelazy == 1)
		{
			add_filter( 'jetpack_lazy_images_blacklisted_classes', array($this, 'modify_jetpack_3dcarousel_lazy_classes'), 10, 3 );
		}
	}
	
	function register_menu()
	{
		$settings = $this->get_settings();
		$userrole = $settings['userrole'];
		
		$menu = add_menu_page(
				__('WonderPlugin 3D Carousel', 'wonderplugin_3dcarousel'),
				__('WonderPlugin 3D Carousel', 'wonderplugin_3dcarousel'),
				$userrole,
				'wonderplugin_3dcarousel_overview',
				array($this, 'show_overview'),
				WONDERPLUGIN_3DCAROUSEL_URL . 'images/logo-16.png' );
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_3dcarousel_overview',
				__('Overview', 'wonderplugin_3dcarousel'),
				__('Overview', 'wonderplugin_3dcarousel'),
				$userrole,
				'wonderplugin_3dcarousel_overview',
				array($this, 'show_overview' ));
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_3dcarousel_overview',
				__('New 3D Carousel', 'wonderplugin_3dcarousel'),
				__('New 3D Carousel', 'wonderplugin_3dcarousel'),
				$userrole,
				'wonderplugin_3dcarousel_add_new',
				array($this, 'add_new' ));
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_3dcarousel_overview',
				__('Manage 3D Carousels', 'wonderplugin_3dcarousel'),
				__('Manage 3D Carousels', 'wonderplugin_3dcarousel'),
				$userrole,
				'wonderplugin_3dcarousel_show_items',
				array($this, 'show_items' ));
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_3dcarousel_overview',
				__('Import/Export', 'wonderplugin_3dcarousel'),
				__('Import/Export', 'wonderplugin_3dcarousel'),
				'manage_options',
				'wonderplugin_3dcarousel_import_export',
				array($this, 'import_export' ) );
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_3dcarousel_overview',
				__('Settings', 'wonderplugin_3dcarousel'),
				__('Settings', 'wonderplugin_3dcarousel'),
				'manage_options',
				'wonderplugin_3dcarousel_edit_settings',
				array($this, 'edit_settings' ) );
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		
		$menu = add_submenu_page(
				null,
				__('View 3D Carousel', 'wonderplugin_3dcarousel'),
				__('View 3D Carousel', 'wonderplugin_3dcarousel'),	
				$userrole,	
				'wonderplugin_3dcarousel_show_item',	
				array($this, 'show_item' ));
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				null,
				__('Edit 3D Carousel', 'wonderplugin_3dcarousel'),
				__('Edit 3D Carousel', 'wonderplugin_3dcarousel'),
				$userrole,
				'wonderplugin_3dcarousel_edit_item',
				array($this, 'edit_item' ) );
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
	}
	
	function register_script()
	{		
		wp_register_script('wonderplugin-3dcarousel-template-script', WONDERPLUGIN_3DCAROUSEL_URL . 'app/wonderplugin-3dcarousel-template.js', array('jquery'), WONDERPLUGIN_3DCAROUSEL_VERSION, false);
		wp_register_script('wonderplugin-3dcarousel-lightbox-script', WONDERPLUGIN_3DCAROUSEL_URL . 'engine/wp3dcarousellightbox.js', array('jquery'), WONDERPLUGIN_3DCAROUSEL_VERSION, false);
		wp_register_script('wonderplugin-3dcarousel-script', WONDERPLUGIN_3DCAROUSEL_URL . 'engine/wonderplugin3dcarousel.js', array('jquery'), WONDERPLUGIN_3DCAROUSEL_VERSION, false);
		wp_register_style('wonderplugin-3dcarousel-style', WONDERPLUGIN_3DCAROUSEL_URL . 'engine/wonderplugin3dcarousel.css');
		wp_register_script('wonderplugin-3dcarousel-creator-script', WONDERPLUGIN_3DCAROUSEL_URL . 'app/wonderplugin-3dcarousel-creator.js', array('jquery'), WONDERPLUGIN_3DCAROUSEL_VERSION, false);
		wp_register_style('wonderplugin-3dcarousel-admin-style', WONDERPLUGIN_3DCAROUSEL_URL . 'wonderplugin-3dcarousel.css');
	}
	
	function enqueue_script()
	{
		wp_enqueue_style('wonderplugin-3dcarousel-style');
		
		$addjstofooter = get_option( 'wonderplugin_3dcarousel_addjstofooter', 0 );
		if ($addjstofooter == 1)
		{
			wp_enqueue_script('wonderplugin-3dcarousel-lightbox-script', false, array(), false, true);
			wp_enqueue_script('wonderplugin-3dcarousel-script', false, array(), false, true);
		}
		else
		{
			wp_enqueue_script('wonderplugin-3dcarousel-lightbox-script');
			wp_enqueue_script('wonderplugin-3dcarousel-script');
		}
	}
	
	function enqueue_admin_script($hook)
	{
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_style ('wp-jquery-ui-dialog');
		
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
		wp_enqueue_script('wonderplugin-3dcarousel-template-script');
		wp_enqueue_script('wonderplugin-3dcarousel-lightbox-script');
		wp_enqueue_script('wonderplugin-3dcarousel-script');
		wp_enqueue_script('wonderplugin-3dcarousel-creator-script');
		wp_enqueue_style('wonderplugin-3dcarousel-style');
		wp_enqueue_style('wonderplugin-3dcarousel-admin-style');
	}
	
	function generate_lightbox_options()
	{
		return '<div class="wp3dcarousellightbox_options" data-skinsfoldername=""  data-jsfolder="' . WONDERPLUGIN_3DCAROUSEL_URL . 'engine/" style="display:none;"></div>';
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
		$this->wonderplugin_3dcarousel_controller->add_metaboxes();
	}
	
	function replace_thickbox_text($translated_text, $text, $domain) {
		
		if ('Insert into Post' == $text) {
			$referer = strpos( wp_get_referer(), 'wonderplugin-3dcarousel' );
			if ( $referer != '' ) {
				return __('Insert into carousel', 'wonderplugin_3dcarousel' );
			}
		}
		return $translated_text;
	}
	
	function show_overview() {
		
		$this->wonderplugin_3dcarousel_controller->show_overview();
	}
	
	function show_items() {
		
		$this->wonderplugin_3dcarousel_controller->show_items();
	}
	
	function add_new() {
		
		$this->wonderplugin_3dcarousel_controller->add_new();
	}
	
	function show_item() {
		
		$this->wonderplugin_3dcarousel_controller->show_item();
	}
	
	function edit_item() {
	
		$this->wonderplugin_3dcarousel_controller->edit_item();
	}
	
	function edit_settings() {
	
		$this->wonderplugin_3dcarousel_controller->edit_settings();
	}
	
	function register() {
	
		$this->wonderplugin_3dcarousel_controller->register();
	}
	
	function get_settings() {
	
		return $this->wonderplugin_3dcarousel_controller->get_settings();
	}
	
	function modify_jetpack_3dcarousel_lazy_classes( $classes ) {
		
		if (empty( $classes ))
			$classes = array();
		$classes[] = "wonderplugin3dcarousel-img";
		return $classes;
	}
	
	function shortcode_handler($atts) {
		
		if ( !isset($atts['id']) && !isset($atts['name']) )
			return __('Please specify a carousel id or name', 'wonderplugin_3dcarousel');
		
		return $this->generate_lightbox_options() . "\r\n" . $this->wonderplugin_3dcarousel_controller->generate_body_code( (isset($atts['id']) ? $atts['id'] : null), (isset($atts['name']) ? $atts['name'] : null), false);
	}
	
	function wp_ajax_save_item() {
		
		check_ajax_referer( 'wonderplugin-3dcarousel-ajaxnonce', 'nonce' );
		
		$settings = $this->get_settings();
		$userrole = $settings['userrole'];
		if ( !current_user_can($userrole) )
			return;
		
		$jsonstripcslash = get_option( 'wonderplugin_3dcarousel_jsonstripcslash', 1 );
		if ($jsonstripcslash == 1)
			$json_post = trim(stripcslashes($_POST["item"]));
		else
			$json_post = trim($_POST["item"]);
		$json_post = str_replace("\\\\", "\\\\\\\\", $json_post);
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
		
		add_filter('safe_style_css', 'wonderplugin_3dcarousel_css_allow');
		add_filter('wp_kses_allowed_html', 'wonderplugin_3dcarousel_tags_allow', 'post');
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
					else if ( is_string($value) )
						$value = wp_kses_post($value);
				}
			}
		}
		remove_filter('wp_kses_allowed_html', 'wonderplugin_3dcarousel_tags_allow', 'post');
		remove_filter('safe_style_css', 'wonderplugin_3dcarousel_css_allow');
		
		header('Content-Type: application/json');
		echo json_encode($this->wonderplugin_3dcarousel_controller->save_item($items));
		wp_die();
		
	}
	
	function import_export() {
	
		$this->wonderplugin_3dcarousel_controller->import_export();
	}
	
	function export_3dcarousel() {
	
		check_admin_referer('wonderplugin-3dcarousel', 'wonderplugin-3dcarousel-export');
	
		if ( !current_user_can('manage_options') )
			return;
	
		$this->wonderplugin_3dcarousel_controller->export_3dcarousel();
	}
}

/**
 * Init the plugin
 */
$wonderplugin_3dcarousel_plugin = new WonderPlugin_3DCarousel_Plugin();

/**
 * Uninstallation
 */
function wonderplugin_3dcarousel_uninstall() {
	
	if ( ! current_user_can( 'activate_plugins' ) )
		return;
	
	global $wpdb;
	
	$keepdata = get_option( 'wonderplugin_3dcarousel_keepdata', 1 );
	if ( $keepdata == 0 )
	{
		$table_name = $wpdb->prefix . "wonderplugin_3dcarousel";
		$wpdb->query("DROP TABLE IF EXISTS $table_name");
	}
}

if ( function_exists('register_uninstall_hook') )
{
	register_uninstall_hook( __FILE__, 'wonderplugin_3dcarousel_uninstall' );
}

define('WONDERPLUGIN_3DCAROUSEL_VERSION_TYPE', 'F');
