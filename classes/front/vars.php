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
	 * Class for container wrapping our comment system
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	static public $wrap_class = 'epoch-wrapper';

	/**
	 * ID for the element containing the comment count
	 *
	 * @since 0.2.2
	 *
	 * @var string
	 */
	static public $count_id = 'epoch-count';

	/**
	 * ID for container wrapping our comment system
	 *
	 * @since 0.0.6
	 *
	 * @var string
	 */
	static public $wrap_id = 'epoch-wrap';

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
	static public $form_id = 'commentform';

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
	 * ID for reply link.
	 *
	 * @since 0.0.5
	 *
	 * @var string
	 */
	static public $reply_link_id = 'epoch-reply-link';

	/**
	 * ID of div we use to get the width of the content area.
	 *
	 * @since 0.0.6
	 *
	 * @var string
	 */
	static public $sniffer = 'epoch-width-sniffer';

	/**
	 * ID of div we use for our loading spinner
	 *
	 * @since 1.1.8
	 *
	 * @var string
	 */
	static public $loading = 'epoch-loading';

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
	 * IMPORTANT: URL is not escaped here, please late escape it, or you are wrong and should feel wrong.
	 *
	 * @deprecated
	 *
	 * @since 0.0.1
	 *
	 * @param bool $submit_comment Optional. If true, the get var for comment submission is added. Default is false.
	 *
	 * @return string|void
	 */
	static public function api_url( $submit_comment = false ) {
		_deprecated_function( __FUNCTION__, '1.0.2.', '\postmatic\epoch\front\class api_paths::api_url' );
		$url =  home_url( self::$endpoint );

		/**
		 * Filter the API URL for where we process our AJAX
		 *
		 * NOTE: Runs before GET vars are, in some cases added to string.
		 *
		 * @since 0.0.5
		 *
		 * @param string $url URL for API
		 */
		add_filter( 'epoch_api_url', $url );
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
