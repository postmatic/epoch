<?php
/**
 * Base class for settings save and sanitization
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2016 Transitive, Inc.
 */
namespace postmatic\epoch\two\admin;


use postmatic\epoch\two\epoch;

class sanitation extends settings{

	/**
	 * Filter saving of our option
	 *
	 * @since 2.0.0
	 *
	 * @param array $new_value
	 *
	 * @return array
	 */
	public function apply( $new_value ){
		if( ! is_array( $new_value ) ){
			return epoch::get_instance()->get_options();
		}

		foreach( $new_value as $key => $value ){
			if( ! in_array( $key, $this->settings_keys  ) ){
				unset( $key );
				continue;
			}

			if( 'order' == $key && ! in_array( $value, array( 'ASC', 'DESC' )  ) ) {
				unset( $key );
			}elseif( 'per_page' == $key && ! is_numeric( $value ) ){
				unset( $key );
			}elseif ( 'before_text' == $key && ! is_string( $value ) ){
				unset( $key );
			}elseif ( 'before_text' == $key && is_string( $value ) ){
				$new_value[ $key ] = wp_kses_post( $value );
			}elseif( 'infinity_scroll' == $key ){
				$new_value[ $key ] = intval( $value  );
			}


		}

		return $new_value;

	}

}
