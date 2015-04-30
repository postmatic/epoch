<?php
/**
 * Epoch Options.
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Postmatic
 */

namespace postmatic\epoch;

/**
 * Options class.
 *
 * @package Epoch
 * @author  Postmatic
 */
class options {

	public static $option_name = 'epoch';

	/**
	 * Update settings
	 *
	 * @since 0.2.0
	 *
	 * @param array $config Single item config.
	 */
	public static function update( $config ) {
		return update_option( self::$option_name, $config );

	}

	/**
	 * Get settings
	 *
	 * @since 0.2.0
	 *
	 * @return mixed|void
	 */
	public static function get() {
		return get_option( self::$option_name );
	}



}
