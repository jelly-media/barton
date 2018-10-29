<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Frizzly - Social Share Buttons
 * Plugin URI:        http://www.highfiveplugins.me
 * Description:       Great-looking social share icons all over your website.
 * Version:1.1.0
 * Author:            Marcin Skrzypiec
 * Author URI:        http://www.highfiveplugins.me
 * Text Domain:       frizzly
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) exit;

if ( ! class_exists( 'Frizzly_Loader' ) ) :

	final class Frizzly_Loader {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance )) {
				self::$instance = new Frizzly_Loader();
			}
			return self::$instance;
		}

		private function __construct(){
			require_once 'includes/Frizzly.php';
			$version = '1.1.0';
			$name = 'Frizzly';
			$frizzly = new Frizzly($name, $version, __FILE__);

			$this->load_textdomain();
		}

		private function load_textdomain() {
			load_plugin_textdomain( 'frizzly', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}
	}

	function frizzly_activation_hook() {
		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

		set_transient( '_frizzly_activation_redirect', true, 30 );
	}
	register_activation_hook( __FILE__, 'frizzly_activation_hook' );

endif; // End if class_exists check

Frizzly_Loader::instance();