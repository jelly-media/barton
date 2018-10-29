<?php

class Frizzly_Admin {

	private $admin_settings_screen;
	private $admin_post_edit_screen;
	private $admin_modules;

	private $name;
	private $version;
	private $file;

	function __construct( $name, $version, $file ) {
		$this->name    = $name;
		$this->version = $version;
		$this->file    = $file;
	}

	public function init() {
		require_once 'includes/Frizzly_Ajax_Result_Builder.php';
		require_once 'includes/Frizzly_Admin_Notice.php';
		require_once 'includes/Frizzly_Validator.php';
		require_once 'Frizzly_Admin_Modules.php';
		require_once 'Frizzly_Welcome_Screen.php';
		require_once 'screens/Frizzly_Admin_Settings_Screen.php';
		require_once 'screens/Frizzly_Admin_Post_Edit_Screen.php';

		$this->admin_modules = new Frizzly_Admin_Modules();
		$this->admin_modules->init();

		$this->admin_settings_screen = new Frizzly_Admin_Settings_Screen( $this->name, $this->version, $this->file, $this->admin_modules );
		$this->admin_settings_screen->init();

		$this->admin_post_edit_screen = new Frizzly_Admin_Post_Edit_Screen( $this->name, $this->version, $this->file );
		$this->admin_post_edit_screen->init();

		new Frizzly_Welcome_Screen( $this->file, $this->version );
	}
}