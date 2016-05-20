<?php
/**
 * Load the plugin
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2016 Transitive, Inc.
 */

use postmatic\epoch\two\epoch;

/**
 * Register autoloader
 *
 * @since 2.0.0
 */
spl_autoload_register(function ($class) {

	$prefix = 'postmatic\\epoch\\two\\';


	$base_dir = EPOCH_DIR . 'src/';


	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {

		return;
	}

	$relative_class = substr($class, $len);
	$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
	if (file_exists($file)) {
		require $file;
	}


});

/**
 * Start plugin
 *
 * @since 2.0.0
 */
epoch::get_instance();

/**
 * When creating comments via REST API, if possible, set the author ID and IP properly.
 *
 *
 * @since 2.0.0
 */
add_filter( 'rest_pre_insert_comment', function ( $prepared_comment, $request ) {
	if ( isset( $_POST, $_POST[ 'epoch' ] ) ) {
		if ( empty( $prepared_comment[ 'comment_author' ] ) ) {
			$email = $prepared_comment[ 'comment_author_email' ];
			if ( is_email( $email ) ) {
				$user = get_user_by( 'email', $email );
				if ( is_object( $user ) ) {
					$prepared_comment[ 'user_id' ] = $user->ID;
					$prepared_comment[ 'comment_author' ] = $user->display_name;
					$prepared_comment[ 'comment_author_url' ] = $user->user_url;
				}
			}

		}

		$ip = '127.0.0.1';

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		$prepared_comment[ 'comment_author_IP' ] = $ip;

	}

	return $prepared_comment;

}, 10, 2 );


/**
 * Add our custom wp_die handler for when comments are being POSTed
 *
 * @since 2.0.0
 */
if(  isset( $_POST, $_POST[ 'epoch' ] ) ){
	add_filter( 'wp_die_handler', 'epoch_wp_die_filter' );
}

/**
 * Set our custom wp_die() handler
 *
 * @since 2.0.0
 *
 * @param $callback
 *
 * @return string
 */
function epoch_wp_die_filter( $callback ){
	if( isset( $_POST, $_POST[ 'epoch' ] ) ){
		return 'epoch_die';
	}

	return $callback;

}

/**
 * Callback for custom wp_die handler
 *
 * @since 2.0.0
 *
 * @param string $message
 * @param string $title
 * @param string $args
 */
function epoch_die( $message, $title, $args ){
	$response = new WP_REST_Response();
	if( isset( $args[ 'response' ]  ) && $args[ 'response' ] ){
		$status =  $args[ 'response' ] ;
	}else{
		$status = 400;
	}
	status_header( $status );
	$response->set_status( $status );

	$response->set_data( array( 'message' => $message ) );
	$result = wp_json_encode( $response );
	echo $result;
	exit;
	
}
