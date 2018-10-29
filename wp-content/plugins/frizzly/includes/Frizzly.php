<?php

class Frizzly {

    private $admin;
    private $name;
    private $version;
    private $file;

    function __construct($name, $version, $file){
        $this->name = $name;
        $this->version = $version;
        $this->file = $file;
        $this->load_dependencies();

        add_action( 'plugins_loaded', array( $this, 'update_plugin' ) );
    }

    private function load_dependencies(){
        require_once 'includes/Frizzly_Includes.php';
        new Frizzly_Includes();


        require_once 'ajax/Frizzly_Ajax.php';
        new Frizzly_Ajax();

        if (is_admin()){
            require_once 'admin/Frizzly_Admin.php';
            $this->admin = new Frizzly_Admin($this->name, $this->version, $this->file);
            $this->admin->init();
        } else {
            require_once 'client/Frizzly_Client.php';
            new Frizzly_Client($this->version, $this->file);
        }
    }

    function update_plugin() {
	    $updater = new Frizzly_Version_Updater( $this->version );
	    $updater->update();
    }
}