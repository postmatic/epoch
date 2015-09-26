<?php
/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace postmatic\epoch\front;


class api_paths extends vars {



	/**
	 * Get URL for the API
	 *
	 * IMPORTANT: URL is not escaped here, please late escape it, or you are wrong and should feel wrong.
	 *
	 * @since 1.0.1
	 *
	 * @param bool $submit_comment Optional. If true, the get var for comment submission is added. Default is false.
	 *
	 * @return string|void
	 */
	static public function api_url( $submit_comment = false ) {

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
		$url = apply_filters( 'epoch_api_url', $url );
		if ( $submit_comment ) {
			$args = array(
				self::$nonce_field => self::make_nonce(),
				'action' => 'submit_comment'
			);
			$url = add_query_arg( $args, $url );

		}

		return $url;

	}

	/**
	 * If possible, return the URL to check this post's comments from a txt file.
	 *
	 * @since 1.0.2
	 *
	 * @param $post_id
	 * @param bool $url Optional. If true, the default, URL is returned. If false the directory path is returned.
	 *
	 * @return string
	 */
	static public function comment_count_alt_check_url( $post_id, $url = true ) {
		if ( 0 < absint( $post_id ) &&  EPOCH_ALT_COUNT_CHECK_MODE ){
			return self::comment_count_dir( $url ) . $post_id . '.txt';

		}

	}

	/**
	 * Get the URL for the directory we use to save comment counts in.
	 *
	 * @since 1.0.2
	 *
	 * @param bool $url Optional. If true, the default, URL is returned. If false the directory path is returned.
	 *
	 * @return string
	 */
	static public  function comment_count_dir( $url = true ) {
		$upload_dir = wp_upload_dir();
		if ( $url ) {
			$dir = $upload_dir[ 'baseurl' ];
		} else {
			$dir = $upload_dir[ 'basedir' ];
		}

		$dir = trailingslashit( $dir ) . 'epoch/';

		/**
		 * Filter the location for comment count files
		 *
		 * @since 1.0.2
		 *
		 * @param string $url
		 * @param bool $url Optional. If true, the default, URL is returned. If false the directory path is returned.
		 */
		return apply_filters( 'epoch_comment_count_dir', $dir, $url );

	}

}
