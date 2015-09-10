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
	 * @return array
	 */
	public static function get() {
		return get_option( self::$option_name, array() );
	}

	/**
	 * Get display options with defaults set if needed.
	 *
	 * @since 0.0.6
	 *
	 * @return array
	 */
	public static function get_display_options() {
		$_options = self::get();
		if ( is_array( $_options ) && isset( $_options[ 'options' ] ) ) {
			$options = $_options[ 'options' ];
		} else {
			$options = array();
		}

		$defaults = array(
			'theme'       => 'light',
			'threaded'    => false,
			'before_text' => __( 'Join the conversation', 'epoch' ),
			'interval'    => 15,
			'order'       => 'ASC',
			'show_pings'  => false,
		);

		$options = wp_parse_args( $options, $defaults );

		return $options;

	}


}
