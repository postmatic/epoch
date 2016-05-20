<?php


use postmatic\epoch\two\epoch;

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


epoch::get_instance();
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
