<?php
/**
 * Process requests from internal API
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2015 Transitive, Inc.
 */
namespace postmatic\epoch\front;


class api_process {

	/**
	 * Get comment form HTML
	 *
	 * @since 0.0.1
	 *
	 * @param array $data Sanitized data from request
	 *
	 * @return array
	 */
	public static function form( $data ) {
		$args = array(
			'id_form' => vars::$form_id,
			'id_submit ' => vars::$submit_id,
		);

		ob_start();
		comment_form( $args , $data[ 'postID' ] );
		$html = ob_get_clean();
		return array(
			'html' => $html
		);
	}

	/**
	 * Get comments
	 *
	 * @since 0.0.1
	 *
	 * @param array $data Sanitized data from request
	 *
	 * @return array
	 */
	public static function get_comments( $data ) {
		$args = array(
			'post_id' => $data[ 'postID' ],
			'order' => 'ASC'
		);

		if ( isset( $data[ 'ignore' ] ) && is_array( $data[ 'ignore' ] ) ){
			$args[ 'comment__not_in' ] = $data[ 'ignore' ];
		}

		$comments = get_comments( $args );
		if ( ! empty( $comments ) && is_array( $comments ) ) {

			foreach( $comments as $i => $comment ) {
				$comment = (array) $comment;
				$comment[ 'author_avatar' ] = get_avatar( $comment[ 'comment_author_email' ] );
				$comments[ $i ] = (object) $comment;
			}

			$comments = wp_json_encode( $comments );
		}

		return array(
			'comments' => $comments,
		);

	}

	/**
	 * Get comment count
	 *
	 * @since 0.0.1
	 *
	 * @param array $data Sanitized data from request
	 *
	 * @return array
	 */
	public static function comment_count( $data ) {
		$count = wp_count_comments( $data[ 'postID' ] );
		return array(
			'count' => (int) $count->approved
		);
	}

	/**
	 * Check if comments are open for a post.
	 *
	 * @since 0.0.1
	 *
	 * @param array $data Sanitized data from request
	 *
	 * @return bool
	 */
	public static function comments_open( $data ) {
		$open = comments_open( $data[ 'postID' ] );
		return $open;
	}

	/**
	 * Submit a comment
	 *
	 * @since 0.0.1
	 *
	 * @param array $data <em>Unsanitized</em> POST data from request
	 *
	 * @return array|bool
	 */
	public static function submit_comment( $data ) {
		if (! isset( $data[ 'comment_post_ID' ] ) ) {
			return false;
		}

		$data       = self::pre_validate_comment( $data );
		$data       = wp_filter_comment( $data );
		if ( is_array( $data ) ) {
			$comment_id = wp_insert_comment( $data );
			$comment    = get_comment( $comment_id );

			if ( $comment_id ) {
				return array(
					'comment_id' => $comment_id,
					'comment'    => $comment
				);

			} else {
				return false;

			}
		} else {
			return false;

		}

	}

	/**
	 * Validate comment data before passing to wp_filter_comment()
	 *
	 * @since 0.0.1
	 *
	 * @access protected
	 *
	 * @param array $data Comment data
	 *
	 * @return array|bool Comment data or false if not possible to validate.
	 */
	protected static function pre_validate_comment( $data ) {
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

		//what we can't live without
		if ( ! isset(
			$data[ 'comment_post_ID' ],
			$data[ 'comment_author' ]

		) ) {
			return false;

		}


		//set the rest manually
		if ( ! isset( $data[ 'comment_date' ] ) || ! $data[ 'comment_date' ] ) {
			$data[ 'comment_date' ] = current_time( 'mysql' );
		}

		if ( ! isset( $data['comment_date_gmt'] ) || ! $data[ 'comment_date_gmt' ] ) {
			$data[ 'comment_date_gmt' ] = get_gmt_from_date ( $data[ 'comment_date' ] );
		}

		foreach( array(
			'comment_author_email',
			'comment_author_url',
			'comment_author_IP',
			'comment_agent',
			'comment_parent'
		) as $field ) {
			if ( ! isset( $data[ $field ] ) || ! $data[ $field ] ) {
				$data[ $field ] = ' ';
			}

		}

		return $data;

	}

}
