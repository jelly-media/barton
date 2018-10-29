<?php

class Frizzly_Ajax {

	function __construct() {
		require_once 'Frizzly_Ajax_Handler.php';
		require_once 'Frizzly_Share_By_Email_Ajax_Handler.php';
		new Frizzly_Share_By_Email_Ajax_Handler();
	}
}