<?php
/**
Plugin Name: Epoch
Plugin Version 2.0.0-a-2
 */


define( 'EPOCH_SLUG', plugin_basename( __FILE__ ) );
define( 'EPOCH_URL', plugin_dir_url( __FILE__ ) );
define( 'EPOCH_DIR', plugin_dir_path( __FILE__ ) );
define( 'EPOCH_VERSION', '2.0.0-a-2' );


add_action( 'plugins_loaded', 'epoch_two' );
function epoch_two() {
	//@TODO version check!
	include EPOCH_DIR . '/bootstrap.php';

}



