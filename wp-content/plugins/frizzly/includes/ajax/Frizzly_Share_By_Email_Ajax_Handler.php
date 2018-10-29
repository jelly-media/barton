<?php

class Frizzly_Share_By_Email_Ajax_Handler extends Frizzly_Ajax_Handler {

    function __construct() {
	    parent::__construct( 'share_by_email' );
    }

	function handle_action() {
    	$post_id = intval( $_POST['postId'] );
    	$to_email = sanitize_email( $_POST['toEmail'] );
    	$from_email =  sanitize_email( $_POST['fromEmail'] );
    	$from_name = sanitize_text_field( $_POST['fromName'] );

    	if (! is_email( $to_email) ) {
    		return $this->return_error( __('Recipient address is not a valid email.', 'frizzly') );
	    }
		if (! is_email( $from_email) ) {
			return $this->return_error( __('Your address is not a valid email.', 'frizzly') );
		}
		if ( '' === $from_name ) {
			return $this->return_error( __('Your name is empty.', 'frizzly') );
		}
		$post = get_post( $post_id );
		$email_content = $this->get_email_content( $post, $from_name, $from_email);
    	$this->send_email($post->post_title, $email_content, $to_email, $from_email, $from_name);
		return $this->return_success( __('Thanks for sharing!', 'frizzly') );
	}

	function return_error($message) {
    	return array(
    		'status' => 'error',
		    'message' => $message
	    );
	}

	function return_success($message) {
		return array(
			'status' => 'success',
			'message' => $message
		);
	}

	function send_email($post_title, $content, $to_email, $from_email, $from_name) {
		// Borrowed from wp_mail();
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}
		$local_email = apply_filters( 'wp_mail_from', 'wordpress@' . $sitename );
		$headers[] = sprintf( 'From: %1$s <%2$s>', $from_name, $local_email );
		$headers[] = sprintf( 'Reply-To: %1$s <%2$s>', $from_name, $from_email );
		wp_mail( $to_email, '['.__( 'Shared Post', 'frizzly' ).'] '. $post_title, $content, $headers );
	}

	/**
	 * @param $post WP_Post
	 * @param $from_name string
	 * @param $from_email string
	 *
	 * @return string
	 */
	function get_email_content( $post, $from_name, $from_email ) {
		$content  = sprintf( __( '%1$s (%2$s) thinks you may be interested in the following post:', 'frizzly' ), $from_name, $from_email );
		$content .= "\n\n";
		$content .= $post->post_title."\n";
		$content .= get_permalink( $post->ID )."\n";
		return $content;
	}
}