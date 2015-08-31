<?php
/**
 * Loads the plugin if dependencies are met.
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2015 Transitive, Inc.
 */

//autoload dependencies
if ( file_exists( EPOCH_PATH . 'vendor/autoload.php' ) ){
	require_once( EPOCH_PATH . 'vendor/autoload.php' );
}


// initialize plugin
\postmatic\epoch\core::get_instance();

if ( ! defined( 'EPOCH_ALT_COUNT_CHECK_MODE' ) ) {

	/**
	 * Whether to save comment counts to text files and attempt to use them to check comment counts.
	 *
	 * NOTE: Experimental. Do not use.
	 *
	 * @since 1.0.1
	 */
	define( 'EPOCH_ALT_COUNT_CHECK_MODE', false );

}
