<?php

class Frizzly_Version_Updater {

	private $option_name = 'frizzly_version';
	private $version;

	function __construct($version) {
		$this->version = $version;
	}

	function update() {
		$version = get_option( $this->option_name, '1.0.1' );

		if ($this->version == $version) {
			return;
		}

		if ( version_compare( $version, '1.1.0', 'lt' ) ) {
			$this->update_1_1_0();
		}

		update_option( $this->option_name, $this->version );
	}

	private function update_1_1_0() {
		set_transient( '_frizzly_activation_redirect', true, 30 );
	}
}