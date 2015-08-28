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


use postmatic\epoch\options;

class api_helper {

	/**
	 * Cache group to use in this class
	 *
	 * @acces protected
	 *
	 * @since 1.0.1
	 *
	 * @var string
	 */
	protected static $cache_group = 'epoch_api_helper';

	/**
	 * Change the data returned to make our lives easier client side.
	 *
	 * @since 0.0.2
	 *
	 * @param array $comments
	 * @param bool $flatten Optional. If true, will remove all hierarchy. Default is false.
	 *
	 * @return array
	 */
	public static function improve_comment_response( $comments, $flatten = false ) {

		foreach ( $comments as $i => $comment ) {
			$comment = (array) $comment;
			$comment = self::add_data_to_comment( $comment, $flatten );
			$comments[ $i ] = (object) $comment;

		}

		return $comments;

	}

	/**
	 * Add extra fields we need in the front-end.
	 *
	 * @since 0.0.4
	 *
	 * @param array|object $comment Comment as array
	 * @param bool $flatten Optional. If true, will remove all hierarchy. Default is false.
	 *
	 * @return array Comment as array with extra fields.
	 */
	public static function add_data_to_comment( $comment, $flatten = false ) {

		if ( is_object( $comment ) ) {
			$comment = (array) $comment;
		}

		$key = ( __FUNCTION__ . $comment['comment_post_ID'] . $comment['comment_ID'] );
		if ( false !=  ( $_comment = wp_cache_get( $key, self::$cache_group  ) ) ) {
			return $_comment;

		}

		$max_depth = (int) get_option( 'thread_comments_depth', 5 );

		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );
		$time = strtotime( $comment['comment_date'] );

		//filter content (make_clickable, wpautop, etc)
		$comment[ 'comment_content' ] = apply_filters( 'comment_text', $comment[ 'comment_content' ], (object)$comment );

		//use display name for comment
		$comment[ 'comment_author' ] = self::find_user_display_name( $comment );

		//add avatar markup as a string
		$comment[ 'author_avatar' ] = get_avatar( $comment[ 'comment_author_email'], 48 );

		//format date according to WordPress settings
		/* translators: 1: date, 2: time */
		$comment[ 'comment_date' ] = sprintf(
			__( '%1$s at %2$s', 'epoch' ),
			date( $date_format, $time ),
			date( $time_format, $time )
		);

		//get comment link
		$comment[ 'comment_link' ] = get_comment_link( $comment['comment_ID'] );

		//are comments replies allowed
		$comment[ 'reply_allowed' ] = comments_open( $comment['comment_post_ID'] );

		//remove parent_id if $flatten
		if ( $flatten ) {
			$comment[ 'comment_parent' ] = "0";
		}else{
			$parents = self::find_parents( $comment[ 'comment_ID' ] );
			if ( empty( $parents ) ) {
				$comment[ 'depth' ] = 1;
			}else{
				$count = count( $parents );
				if ( $count > $max_depth) {
					$comment[ 'comment_parent' ] = $parents[ $max_depth - 1 ];
					$count = $max_depth;
				}
				$comment[ 'depth' ] = $count;
			}
		}

		//if has no children add that key as false.
		if ( $flatten || ! isset( $comment[ 'children' ] ) ) {
			$comment[ 'children' ] = false;
		}

		$comment['list_class'] = ( $comment['comment_parent'] == '0' ) ? '' : 'children';


		if ( ! $flatten ) {
			//get reply link
			$reply_link_args = array(
				'add_below' => 'comment',
				'max_depth' => $max_depth,
				'depth'     => (int) $comment['depth']
			);

			$comment[ 'reply_link' ] = get_comment_reply_link( $reply_link_args, (int) $comment['comment_ID'] );
		}else{
			$comment[ 'reply_link' ] = '';
		}


		wp_cache_set( $key, $comment, self::$cache_group, HOUR_IN_SECONDS );

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
		if ( ! isset( $data['comment_author'] ) ) {
			if ( is_user_logged_in() ) {
				$user                         = get_userdata( get_current_user_id() );
				$data['user_id']              = $user->ID;
				$data['comment_author']       = $user->user_login;
				$data['comment_author_email'] = $user->user_email;
				$data['comment_author_url']   = $user->user_url;
			} else {
				$data['user_id'] = 0;
				if ( isset( $data['author'] ) ) {
					$data['comment_author'] = $data['author'];
					unset( $data['author'] );
				}
				foreach (
					array(
						'email',
						'url'
					) as $field
				) {
					$_field = 'comment_author_' . $field;
					if ( ! isset( $data[ $_field ] ) || ! $data[ $_field ] ) {
						if ( isset( $data[ $field ] ) ) {
							$data[ $_field ] = $data[ $field ];
							unset( $data[ $field ] );
						}

					}

				}


			}

		}

		if ( ! isset( $data ['comment_content' ] ) || ! $data[ 'comment_content' ] ) {
			if ( isset( $data[ 'comment' ] ) ) {
				$data['comment_content' ] = $data[ 'comment' ];
				unset( $data[ 'comment' ] );
			} else {
				$data[ 'comment_content' ] = ' ';
			}

		}

		if ( ! isset( $data[ 'comment_author_IP' ] ) || ! $data[ 'comment_author_IP' ] ) {
			if ( isset( $_SERVER[ 'REMOTE_ADDR' ] ) ) {
				$data[ 'comment_author_IP' ] = $_SERVER[ 'REMOTE_ADDR' ];
			} else {
				$data[ 'comment_author_IP' ] = ' ';
			}

		}

		if ( empty( $data[ 'comment_type' ] ) ) {
			$data[ 'comment_type' ] = "";
		}

		if ( empty( $data[ 'comment_date' ] ) ) {
			$data[ 'comment_date' ] = current_time( 'mysql' );
		}

		if ( empty( $data[ 'comment_date_gmt' ] ) ) {
			$data[ 'comment_date_gmt' ] = current_time( 'mysql', 1 );
		}

		return $data;

	}

	/**
	 * Construct comment query args
	 *
	 * @since 0.0.11
	 *
	 * @param int $post_id The post ID to fetch from
	 *
	 * @return array
	 */
	public static function get_comment_args( $post_id ) {

		$options = options::get_display_options();

		$args = array(
			'post_id' => $post_id,
			'order'   => $options['order'],
			'status'  => 'approve',
		);

		return $args;

	}

	/**
	 * Check if we should thread comments
	 *
	 * @since 0.0.12
	 *
	 * @return bool
	 */
	public static function thread() {
		if ( get_option( 'thread_comments' ) && 0 != (int) get_option( 'thread_comments_depth' ) ) {
			return true;
		}

	}

	/**
	 * Ensures depth is correct and doesn't exceed max depth. Also resets parent up depth tree if depth does exceed max.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 *
	 * @param $comment_id
	 *
	 * @return array Contains new depth and parent
	 */
	protected static function find_parents( $comment_id ) {
		$parents = array();
		$max = get_option( 'thread_comments_depth', 5 );
		while( $comment_id > 0 ) {
			$comment = get_comment( $comment_id );
			$parent = $comment->comment_parent;
			if ( $parent ) {
				$parents[] = $comment->comment_parent;
			}
			$comment_id = $parent;



		}

		return $parents;


	}

	/**
	 * Try are damndest to find display name for comment author.
	 *
	 * @since 1.0.1
	 *
	 * @param array $comment The comment
	 *
	 * @return string Display name.
	 */
	protected static function find_user_display_name( $comment ) {
		$key = md5( $comment[ 'comment_author_email' ], $comment[ 'comment_author' ] );
		if ( false == ( $display_name = wp_cache_get( $key, 'epoch' ) ) ) {

			$found = false;
			if ( is_email( $comment[ 'comment_author_email' ] ) ){
				$user = get_user_by( 'email', $comment[ 'comment_author_email' ] );
				$_display_name = self::get_display_name( $user );
				if ( $_display_name ) {
					$found = true;
					$display_name = $_display_name;
				}

			}

			if ( ! $found ) {
				$value = $comment[ 'comment_author' ] ;
				foreach( array( 'login', 'id', 'slug', 'email' ) as $field ) {
					$user = \WP_User::get_data_by( $field, $value );
					$_display_name = self::get_display_name( $user );
					if ( $_display_name ) {
						$display_name = $_display_name;
						$found = true;
						break;
					}

				}

			}

			if ( ! $found ) {
				$display_name = $comment[ 'comment_author' ];
			}else{
				wp_cache_set( $key, $display_name, self::$cache_group, HOUR_IN_SECONDS );
			}

		}

		return $display_name;

	}

	/**
	 * Checks if $user is a WP_User object and if so attempts to return their display name.
	 *
	 * @since 1.0.1
	 *
	 * @param bool|\WP_User $user
	 *
	 * @return string|void
	 */
	protected static function get_display_name( $user ) {
		if ( is_a( $user,  '\WP_User' ) ) {
			$display_name = $user->display_name;
			if ( is_string( $display_name ) ) {
				return $display_name;

			}

		}

	}


}
