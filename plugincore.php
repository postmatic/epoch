<?php
/**
Plugin Name: Epoch
Plugin Version 2.0.0-a-2
 */


define( 'EPOCH_SLUG', plugin_basename( __FILE__ ) );
define( 'EPOCH_URL', plugin_dir_url( __FILE__ ) );
define( 'EPOCH_DIR', plugin_dir_path( __FILE__ ) );
define( 'EPOCH_VERSION', '2.0.0-a-3' );

add_action( 'plugins_loaded', 'epoch_two' );

/**
 * Load plugin if dependencies are met
 *
 * @uses "plugins_loaded" action
 *
 * @since 2.0.0
 */
function epoch_two() {
	global $wp_version;
	$messages = array();
	$php_check = version_compare( PHP_VERSION, '5.4.0', '>=' );
	$wp_check = version_compare( $wp_version, '4.4', '>=' );
	$api_check =  class_exists( 'WP_REST_Comments_Controller' );
	if ( ! $php_check  || !  $wp_check || ! $api_check ) {

		if ( ! $php_check ) {
			$messages[] = __( sprintf( 'PHP version 5.4 or later. Current version is %s', PHP_VERSION ), 'epoch' );

		}

		if ( ! $wp_check ) {
			$messages[] = __( sprintf( 'WordPress version 4.4 or later. Current version is %s', $wp_version ), 'epoch' );

		}

		if( ! $api_check ){
			$messages[] = __( 'the WordPress REST API plugin. You can download it from the Plugins menu.', 'epoch' );
		}

		global $epoch_fail;
		$epoch_fail = __( 'Heads up! Epoch requires ', 'epoch' ) . implode( ' and ', $messages  );
		add_action( 'admin_notices', 'epoch_fail_notice' );



	}else{
		include EPOCH_DIR . '/bootstrap.php';
	}


}


/**
 * Output admin notices if needed
 *
 * @uses "admin_notices" action
 *
 * @since 2.0.0
 */
function epoch_fail_notice(){
	global $epoch_fail;
	if( is_string( $epoch_fail ) ){
		$class = 'notice notice-error';

		printf( '<div class="%s"><p>%s</p></div>', $class, esc_html__( $epoch_fail ) );
	}



}
