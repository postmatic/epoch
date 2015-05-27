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


use postmatic\epoch\options;

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
		$options = options::get_display_options();
		if ( 'none' == $options[ 'theme' ] ) {
			return file_get_contents( dirname( __FILE__ ) . '/templates/comment-no-theme.html' );
		}

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

	/**
	 * Get Postmatic subscribe widget.
	 *
	 * @since 0.0.5
	 *
	 * @return string
	 */
	public static function postmatic_widget() {

		$attributes = array(
			'title' => __( 'Subscribe to Comments Via Email', 'epoch' ),
			'collect_name' => true,
			'template_path' => null,
		);


		ob_start();

		the_widget( 'Prompt_Subscribe_Widget', $attributes );

		return ob_get_clean();

	}

	/**
	 * Output an empty container after the content so we can sniff out its width.
	 *
	 * @uses "the_content"
	 *
	 * @since 0.0.6
	 */
	public static function width_sniffer( $content ) {
		return $content . sprintf( '<div id="%1s"></div>', vars::$sniffer );

	}

	/**
	 * Get comment form HTML
	 *
	 * @since 0.0.9
	 *
	 * @param int $post_id Post ID for this comment form
	 *
	 * @return string
	 */
	public static function get_form( $post_id ) {
		if ( 0 < absint( $post_id ) && comments_open( $post_id ) ) {
			$options = options::get_display_options();

			$args = array(
				'id_form'             => vars::$form_id,
				'id_submit '          => vars::$submit_id,
				'comment_notes_after' => '',
			);

			$args['title_reply'] = $options['before_text'];

			ob_start();
			comment_form( $args, $post_id );
			$html = ob_get_clean();
		} else if ( ! comments_open( $post_id ) ) {
			$html = __( 'Comments are closed.', 'epoch' );
		} else {
			$html = '';
		}

		return $html;

	}

}
