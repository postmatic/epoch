<?php
/**
 * Define variables we need in front-end.
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2015 Transitive, Inc.
 */


namespace postmatic\epoch\front;

class vars {

	/**
	 * ID for container wrapping our comment system
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	static public $wrap_class = 'epoch-wrapper';

	/**
	 * ID for container containing the comment form.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	static public $form_wrap = 'epoch-commenting';

	/**
	 * ID for the comment form itself
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	static public $form_id = 'epoch-comments-form';

	/**
	 * ID of the comment submit button
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	static public $submit_id = 'epoch-submit';

	/**
	 * ID for container containing the comments
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	static public $comments_wrap = 'epoch-comments';

	/**
	 * ID for the comment template
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	static public $comments_template_id = 'epoch-comment-template';

	/**
	 * Name of GET var we use to transmit nonce.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	static public $nonce_field = 'epochNonce';

	/**
	 * Endpoint slug for internal API
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	static public $endpoint = 'epoch-api';

	/**
	 * ID of the container with the comment form loading GIF in it
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	static public $comment_form_spinner_id = 'epoch-comment-form-spinner';

	/**
	 * ID of the container with the comments loading GIF in it
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	static public $comments_area_spinner_id = 'epoch-comments-area-spinner';

	/**
	 * Create a nonce
	 *
	 * @return string
	 */
	static public function make_nonce() {
		global $post;
		if ( is_object( $post ) ) {
			$nonce = wp_create_nonce( self::$nonce_field . (int) $post->ID );

			return $nonce;
		}else{
			return rand();
		}

	}

	/**
	 * Get URL for the API
	 *
	 * Note: URL is not escaped here, please late escape it.
	 *
	 * @since 0.0.1
	 *
	 * @param bool $submit_comment Optional. If true, the get var for comment submission is added. Default is false.
	 *
	 * @return string|void
	 */
	static public function api_url( $submit_comment = false ) {
		$url =  home_url( self::$endpoint );
		if ( $submit_comment ) {
			$args = array(
				self::$nonce_field => self::make_nonce(),
				'action' => 'submit_comment'
			);
			$url = add_query_arg( $args, $url );
		}

		return $url;

	}

}
