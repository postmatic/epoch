<?php
/**
 * Layout elements we need
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2015 Transitive, Inc.
 */


namespace postmatic\epoch\front;


class layout {

	/**
	 * The path to the template we output on initial load.
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 */
	public static function initial() {
		return dirname( __FILE__ ) . '/templates/initial.php';

	}

	/**
	 * The path to the template for comments
	 *
	 * @return string
	 */
	public static function comments_template() {
		return file_get_contents( dirname( __FILE__ ) . '/templates/comment.html' );
	}

	/**
	 * Image tag for our spinner gif
	 *
	 * @todo A better GIF
	 *
	 * @param string $id ID attribute for container this spinner is outputted in
	 *
	 * @return string Spinner HTML
	 */
	public static function spinner_img_tag( $id ) {
		$spinner = sprintf( '<div id="%1s" style="display:none;"><img src="%2s" /></div>',
			esc_attr( $id ),
			esc_url( admin_url( 'images/wpspin_light.gif') )
		);

		return $spinner;

	}



}
