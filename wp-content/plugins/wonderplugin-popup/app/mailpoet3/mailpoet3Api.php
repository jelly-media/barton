<?php

function mailpoet3_addsubscriber($user_data, $list, $options) {
		
	\MailPoet\API\API::MP('v1')->addSubscriber($user_data, $list, $options);
}

function mailpoet3_getlists() {
		
	return \MailPoet\API\API::MP('v1')->getLists();
}