<?php
// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'frizzly_general' );
delete_option( 'frizzly_share' );
delete_post_meta_by_key( 'frizzly_social_data' );