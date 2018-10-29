<?php

abstract class Frizzly_Admin_Module {
	public $name;
	public $slug;
	/**
	 * @var Frizzly_Options
	 */
	protected $options;

	/**
	 * @var Frizzly_Admin_Notice[]
	 */
	private $notices;
	/**
	 * @var Frizzly_Admin_Submodule[]
	 */
	protected $submodules;

	function __construct( $slug, $name, $options ) {
		$this->name       = $name;
		$this->slug       = $slug;
		$this->options    = $options;
		$this->submodules = array();
		$this->notices    = array();
	}

	function add_submodule( $submodule ) {
		$this->submodules[ $submodule->slug ] = $submodule;
	}

	function get_submodule( $name ) {
		return $this->submodules[ $name ];
	}

	function get_tabs() {
		$tabs = array();
		foreach ( $this->submodules as $slug => $sub ) {
			$tabs[] = array( 'slug' => $sub->slug, 'name' => $sub->name );
		}

		return $tabs;
	}

	function get_page_i18n( $slug ) {
		return $this->submodules[ $slug ]->get_page_i18n();
	}

	function get_page_settings( $slug ) {
		$options_value     = $this->options->get();
		$options_tab_value = $options_value[ $slug ];

		return $this->submodules[ $slug ]->get_page_settings( $options_tab_value );
	}

	function save_settings( $submodule, $current_value ) {
		$validator = $this->validate( $submodule, $current_value );
		$errors = $validator->get_errors();

		if ( count( $errors ) > 0 ) {
			$error_messages = array_merge(
				array( '<strong>' .__( 'Settings not saved.', 'frizzly' ) . '</strong>' ),
				$errors
			);
			$this->notices[] = new Frizzly_Admin_Notice( 'error', true, join( '<br/>', $error_messages ) );
		} else {
			$this->update_settings_section( $submodule, $validator->get_result() );
			$this->notices[] = new Frizzly_Admin_Notice( 'success', true, '<strong>' . __( 'Settings saved.', 'frizzly' ) . '</strong>' );
		}
	}

	function show_notices( $is_share_module_screen ) {
		foreach ( $this->notices as $notice ) {
			echo $notice->get_html();
		}
	}

	function update_settings_section( $section, $updated ) {
		$options             = $this->options->get();
		$options[ $section ] = $updated;
		$after_update        = $this->options->update( $options );

		return $after_update[ $section ];
	}

	/**
	 * @param $slug $string
	 * @param $current_value array
	 *
	 * @return Frizzly_Validator
	 */
	function validate( $slug, $current_value ) {
		$defaults = $this->options->get_default();

		return $this->submodules[ $slug ]->validate( $current_value, $defaults[ $slug ] );
	}
}

