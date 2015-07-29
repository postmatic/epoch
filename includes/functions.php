<?php
/**
 * Functions for this plugin
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2015 Transitive, Inc.
 */


add_action( 'epoch_iframe_footer', 'epoch_iframe_footer_print_scripts' );
function epoch_iframe_footer_print_scripts() {
	global $wp_scripts;

	/**
	 * Use this filter to add or remove scripts from the Epoch iFrame filter
	 *
	 * Add handles of scripts, already registered with wp_register script here.
	 *
	 * jQuery and Epoch scripts are added after this filter runs
	 *
	 * @since 1.0.0
	 *
	 * @param array $scripts An array of registered script handles
	 */
	$scripts = apply_filters( 'epoch_iframe_scripts', array() );
	$scripts[] = 'epoch-handlebars';
	$scripts[] = 'epoch-handlebars-helpers';
	$scripts[] = 'jquery-core';
	$scripts[] = 'jquery-migrate';
	$scripts[] = 'epoch';
	$scripts[] = 'visibility';
	$scripts[] = 'comment-reply';

	if ( is_array( $scripts ) && ! empty( $scripts ) ) {
		$wp_scripts->reset();
		if ( true == $wp_scripts->do_concat ) {
			$wp_scripts->do_concat = false;
		}
		foreach( $scripts as $handle ) {
			if( !empty( $wp_scripts->registered[ $handle ] ) ){
				$wp_scripts->do_item( $handle );
			}
		}

	}
}
