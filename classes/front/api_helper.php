<?php
/**
 * Helper functions for the API. Data validation, formating etc.
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2015 Transitive, Inc.
 */

namespace postmatic\epoch\front;


class api_helper {

	/**
	 * Change the data returned to make our lives easier client side.
	 *
	 * @since 0.0.2
	 *
	 * @param array $comments
	 *
	 * @return array
	 */
	public static function improve_comment_response( $comments ) {

		foreach ( $comments as $i => $comment ) {
			$comment = (array) $comment;
			$comment = self::add_data_to_comment( $comment );
			$comments[ $i ] = (object) $comment;

		}

		return $comments;

	}

	/**
	 * Add extra fields we need in the front-end.
	 *
	 * @since 0.0.4
	 *
	 * @param array $comment Comment as array
	 *
	 * @return array Comment as array with extra fields.
	 */
	public static function add_data_to_comment( $comment ) {
		$date_format = get_option( 'date_format' );

		$reply_link_args = array(
			'add_below'     => 'comment',
			'depth'         => 1,
			'max_depth'     => get_option( 'thread_comments_depth', 5 )
		);

		//add avatar markup as a string
		$comment[ 'author_avatar' ] = get_avatar( $comment[ 'comment_author_email'] );

		//format date according to WordPress settings
		$comment[ 'comment_date'] = date( $date_format, strtotime( $comment['comment_date'] ) );

		//get comment link
		$comment[ 'comment_link'] = get_comment_link( $comment['comment_ID'] );

		//are comments replies allowed
		$comment[ 'reply_allowed'] = comments_open( $comment['comment_post_ID'] );

		//get reply link
		$comment['reply_link'] = get_comment_reply_link( $reply_link_args, (int) $comment['comment_ID'] );

		//if has no children add that key as false.
		if ( ! isset( $comment[ 'children' ] ) ) {
			$comment[ 'children' ] = false;
		}

		return $comment;

	}

	/**
	 * Validate comment data before passing to wp_filter_comment()
	 *
	 * @since 0.0.1
	 *
	 * @param array $data Comment data
	 *
	 * @return array|bool Comment data or false if not possible to validate.
	 */
	public static function pre_validate_comment( $data ) {
		if ( ! isset( $data[ 'comment_author' ] ) ) {
			if ( is_user_logged_in() ) {
				$user = get_userdata( get_current_user_id() );
				$data[ 'user_id' ] = $user->ID;
				$data[ 'comment_author' ] = $user->user_login;
				$data[ 'comment_author_email' ] = $user->user_email;
				$data[ 'comment_author_url' ] = $user->user_url;
			}else{
				$data[ 'user_id' ] = 0;
				if ( isset( $data[ 'author' ] ) ) {
					$data[ 'comment_author' ] = $data[ 'author' ];
					unset( $data[ 'author' ] );
				}
				foreach( array(
					'email',
					'url'
				) as $field ) {
					$_field = 'comment_author_' . $field;
					if ( ! isset( $data[ $_field ] ) || ! $data[ $_field ]  ) {
						if ( isset( $data[ $field ] ) ) {
							$data[ $_field ] = $data[ $field ];
							unset( $data[ $field ] );
						}

					}

				}


			}

		}

		if ( ! isset( $data[ 'comment_content' ] ) || ! $data[ 'comment_content' ] ) {
			if ( isset( $data[ 'comment' ] ) ) {
				$data[ 'comment_content' ] = $data[ 'comment' ];
				unset( $data[ 'comment' ] );
			} else {
				$data['comment_content'] = ' ';
			}

		}

		if ( ! isset( $data[ 'comment_author_IP' ] ) || ! $data[ 'comment_author_IP' ] ) {
			if ( isset( $_SERVER[ 'REMOTE_ADDR' ] ) ) {
				$data[ 'comment_author_IP' ] = $_SERVER[ 'REMOTE_ADDR' ];
			}else{
				$data[ 'comment_author_IP' ] = ' ';
			}

		}

}
