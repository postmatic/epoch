<?php
/**
 * Save settings
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2016 Transitive, Inc.
 */
namespace postmatic\epoch\two\admin;


class save extends settings {
	
	
	/**
	 * Save the settings
	 *
	 * @since 2.0.0
	 */
	public function save_settings(){
		if( ! $this->check() ){
			wp_die();
		}

		$data = array();
		if( isset( $_POST ) && is_array( $_POST ) ){
			foreach( $this->settings_keys as $key ){
				if( isset( $_POST[ $key ] ) && is_scalar( $key ) ){
					//Sanitation happens further down the stack :) Chill.
					$data[ $key ] = $_POST[ $key ];
				}
			}
		}

		status_header( 201 );
		echo update_option( $this->option_key,  $data );
		exit;


	}

	/**
	 * Check nonce, referrer, cap and secret decoder ring.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function check(){
		if( ! current_user_can( 'manage_options' ) ){
			status_header( 403 );
			return  false;
		}

		$verified = check_ajax_referer( 'epoch-admin', '_wpnonce', false );
		if ( ! $verified ) {
			status_header( 500 );
			return false;
		}

		return true;

	}

}
