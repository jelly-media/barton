<?php

class Frizzly_Admin_Settings_Screen {

	/**
	 * @var Frizzly_Admin_Share_Module
	 */
	private $share_module;

	/**
	 * @var string
	 */
	private $ajax_custom_action;
	private $save_settings_action;
	private $save_settings_tab;
	private $name;
	private $file;
	private $version;
	private $screen_hook;
	private $page_base;

	function __construct( $name, $version, $file, $admin_modules ) {
		$this->ajax_custom_action   = 'frizzly_settings_custom';
		$this->save_settings_action = 'frizzly_settings_save';
		$this->save_settings_tab    = 'frizzly_settings_save_tab';
		$this->page_base            = 'frizzly_settings';
		$this->name                 = $name;
		$this->version              = $version;
		$this->file                 = $file;
		$this->share_module         = new Frizzly_Admin_Share_Module();
		$this->screen_hook          = '';
	}

	function init() {
		add_action( 'admin_init', array( $this, 'save_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'wp_ajax_' . $this->ajax_custom_action, array( $this, 'ajax_custom' ) );
		add_action( 'admin_notices', array( $this, 'show_notices' ) );
		$basename = plugin_basename( $this->file );
		add_filter( "plugin_action_links_$basename", array( $this, 'add_settings_link' ) );
	}

	function add_admin_menu() {
		$this->screen_hook = add_options_page(
			$this->name,
			$this->name,
			'manage_options',
			$this->page_base,
			array( $this, 'print_settings_page' )
		);
	}

	function add_settings_link( $links ) {
		$url  = admin_url( 'options-general.php?page=' . $this->page_base );
		$link = sprintf( '<a href="%s">%s</a>', $url, __( 'Settings', 'frizzly' ) );
		array_unshift( $links, $link );
		return $links;
	}

	function enqueue_admin_scripts( $hook ) {
		if ( $this->screen_hook !== $hook ) {
			return;
		}

		$plugin_dir_url = plugin_dir_url( $this->file );
		wp_enqueue_script( 'frizzly-admin-js', $plugin_dir_url . 'js/frizzly.admin.js', array( 'jquery' ), $this->version, true );

		$tabs = $this->share_module->get_tabs();
		$tab  = isset( $_GET['tab'] ) ? $_GET['tab'] : $tabs[0]['slug'];


		wp_localize_script( 'frizzly-admin-js', 'frizzlySettings', array(
			'ajax'     => array(
				'url'               => admin_url( 'admin-ajax.php' ),
				'customAction'      => $this->ajax_custom_action,
				'customActionNonce' => wp_create_nonce( $this->ajax_custom_action ),
				'tab'               => $tab,
			),
			'save'     => array(
				'post_url' => add_query_arg( array( 'tab' => $tab ) ),
				'action'   => $this->save_settings_action,
				'nonce'    => wp_create_nonce( $this->save_settings_action ),
				'tab'      => $this->save_settings_tab,
				'submit'   => __( 'Save Changes', 'frizzly' )
			),
			'tabs'     => $tabs,
			'tab'      => $tab,
			'page'     => $this->page_base,
			'settings' => $this->share_module->get_page_settings( $tab ),
			'i18n'     => array(
				'editor' => $this->share_module->get_page_i18n( $tab ),
				'links'  => array(
					array(
						'name' => __( 'Documentation', 'frizzly' ),
						'url'  => 'https://highfiveplugins.com/frizzly/frizzly-documentation/'
					),
					array(
						'name' => __( 'Support', 'frizzly' ),
						'url'  => 'https://wordpress.org/support/plugin/frizzly'
					)
				)
			)
		) );

		wp_enqueue_style( 'frizzly-lib-font-awesome', $plugin_dir_url . 'css/libs/font-awesome/css/font-awesome.css', array(), $this->version );
		wp_enqueue_style( 'frizzly-admin-css', $plugin_dir_url . 'css/frizzly.admin.css', array( 'frizzly-lib-font-awesome' ), $this->version );
		wp_enqueue_media();
	}

	function print_settings_page() {
		?>
        <div ng-app="app" class="wrap">
            <h2><?php _e( 'Frizzly', 'frizzly' ); ?></h2>
            <share></share>
        </div>
		<?php
	}

	function ajax_custom() {
		check_ajax_referer( $this->ajax_custom_action, 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$action = $_REQUEST['name'];
		$params = json_decode( stripslashes( $_REQUEST['settings'] ), true );
		$result = apply_filters( 'frizzly_settings_custom_' . $action, array(), $params );
		wp_send_json( $result );
	}

	function save_settings() {
		$return_condition = ! isset( $_POST[ $this->save_settings_action ] ) ||
		                    ! wp_verify_nonce( $_POST[ $this->save_settings_action ], $this->save_settings_action );
		if ( $return_condition ) {
			return;
		}
		$tab = $_POST[ $this->save_settings_tab ];
		$this->share_module->save_settings( $tab, $_POST );
	}

	function show_notices() {
		$screen            = get_current_screen();
		$is_current_screen = $this->screen_hook === $screen->id;
		$this->share_module->show_notices( $is_current_screen );
	}
}