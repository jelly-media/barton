<?php 

require_once 'class-wonderplugin-tabs-model.php';
require_once 'class-wonderplugin-tabs-view.php';
require_once 'class-wonderplugin-tabs-update.php';

class WonderPlugin_Tabs_Controller {

	private $view, $model, $update;

	function __construct() {

		$this->model = new WonderPlugin_Tabs_Model($this);	
		$this->view = new WonderPlugin_Tabs_View($this);
		$this->update = new WonderPlugin_Tabs_Update($this);
		
		$this->init();
	}
	
	function add_metaboxes()
	{
		$this->view->add_metaboxes();
	}
	
	function show_overview() {
		
		$this->view->print_overview();
	}
	
	function show_items() {
	
		$this->view->print_items();
	}
	
	function add_new() {
		
		$this->view->print_add_new();
	}
	
	function show_item()
	{
		$this->view->print_item();
	}
	
	function edit_item()
	{
		$this->view->print_edit_item();
	}
	
	function get_tab_content($post) {
	
		return $this->model->get_tab_content($post);
	}
	
	function edit_settings()
	{
		$this->view->print_edit_settings();
	}
	
	function save_settings($options)
	{
		$this->model->save_settings($options);
	}
	
	function get_settings()
	{
		return $this->model->get_settings();
	}
	
	function register()
	{
		$this->view->print_register();
	}
	
	function check_license($options)
	{
		return $this->model->check_license($options);
	}
	
	function deregister_license($options)
	{
		return $this->model->deregister_license($options);
	}
	
	function save_plugin_info($info)
	{
		return $this->model->save_plugin_info($info);
	}
	
	function get_plugin_info()
	{
		return $this->model->get_plugin_info();
	}
	
	function get_update_data($action, $key)
	{
		return $this->update->get_update_data($action, $key);
	}
	
	function generate_content_shortcode($id) {
		
		return $this->model->generate_content_shortcode($id);
	}
	
	function generate_body_code($id, $name, $content, $data_attributes, $has_wrapper) {
		
		return $this->model->generate_body_code($id, $name, $content, $data_attributes, $has_wrapper);
	}
	
	function get_page_code($id, $autop) {
		
		return $this->model->get_page_code($id, $autop);
	}
	
	function delete_item($id)
	{
		return $this->model->delete_item($id);
	}
	
	function trash_item($id)
	{
		return $this->model->trash_item($id);
	}
	
	function restore_item($id)
	{
		return $this->model->restore_item($id);
	}
	
	function clone_item($id)
	{
		return $this->model->clone_item($id);
	}
	
	function save_item($item)
	{
		return $this->model->save_item($item);	
	}
	
	function get_list_data() {
	
		return $this->model->get_list_data();
	}
	
	function search_replace_items($post)
	{
		return $this->model->search_replace_items($post);
	}
	
	function import_export()
	{
		$this->view->import_export();
	}
	
	function import_tabs($post, $files)
	{
		return $this->model->import_tabs($post, $files);
	}
	
	function export_tabs() {
	
		return $this->model->export_tabs();
	}
	
	function init() {
	
		$engine = array("WordPress Tabs", "WordPress Tabs Plugin", "Responsive WordPress Tabs", "Responsive WordPress Tabs Plugin");
		$option_name = 'wonderplugin-tabs-engine';
		if ( get_option( $option_name ) == false )
			update_option( $option_name, $engine[array_rand($engine)] );
	}
	
	function get_item_data($id) {
		
		return $this->model->get_item_data($id);
	}
}