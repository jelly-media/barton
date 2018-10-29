<?php

class Frizzly_Admin_Share_Module extends Frizzly_Admin_Module {

	/**
	 * @var Frizzly_Share_Options
	 */
	private $meta_nonce_name;
	/**
	 * @var string[]
	 */
	private $meta_submodules;

	function __construct() {
		parent::__construct( 'share', __( 'Share', 'frizzly' ), new Frizzly_Share_Options() );
		$this->meta_nonce_name = 'frizzly_share_meta';
		$this->meta_submodules = array( 'image', 'content' );

		$this->load_dependencies();
		$this->add_submodules();
	}

	function add_submodules() {
		$this->add_submodule( new Frizzly_Admin_General_Submodule() );
		$this->add_submodule( new Frizzly_Admin_Image_Submodule() );
		$this->add_submodule( new Frizzly_Admin_Content_Submodule() );
	}

	function is_current_module_screen() {
		$screen = get_current_screen();

		return 'options-general.php' === $screen->parent_file &&
		       isset( $_GET['page'] ) && 'frizzly_settings' === $_GET['page'];
	}

	function load_dependencies() {
		require_once 'share/Frizzly_Admin_Button_Settings_Submodule_Base.php';
		require_once 'share/Frizzly_Admin_General_Submodule.php';
		require_once 'share/Frizzly_Admin_Content_Submodule.php';
		require_once 'share/Frizzly_Admin_Image_Submodule.php';
	}

	function show_notices( $is_share_module_screen ) {
		parent::show_notices( $is_share_module_screen );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$share_options = $this->options->get();
		foreach ( $this->submodules as $submodule ) {
			$submodule->show_notice( $is_share_module_screen, $share_options );
		}
	}
}