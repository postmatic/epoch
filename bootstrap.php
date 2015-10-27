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

