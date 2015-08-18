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


use postmatic\epoch\options;

class api_process {

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
		if ( !defined( 'DOING_AJAX' ) ) define( 'DOING_AJAX', true );

		$args = api_helper::get_comment_args( $data[ 'postID' ] );

		$options = options::get_display_options();
		$comments = get_comments( $args  );
		if ( 'ASC' == $options[ 'order' ] ) {
			$parents = array_combine( wp_list_pluck( $comments, 'comment_ID'),wp_list_pluck( $comments, 'comment_parent' ) );

			asort( $parents );

			$comments = (array) $comments;
			$comments = array_combine( wp_list_pluck( $comments, 'comment_ID'), $comments );
			$i = 0;
			foreach( $comments as $id => $parent ) {
				$_comments[ $i ] = $comments[ $id ];
				$i++;
			}
			rsort( $_comments );

			$comments = $_comments;
		}


		if ( ! empty( $comments ) && is_array( $comments ) ) {
			$comments = api_helper::improve_comment_response( $comments, ! api_helper::thread() );
			$comments = wp_json_encode( $comments );
		}else{
			return false;
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
		if ( !defined( 'DOING_AJAX' ) ) define( 'DOING_AJAX', true );
		
		$count = wp_count_comments( $data[ 'postID' ] );
		if ( EPOCH_ALT_COUNT_CHECK_MODE ) {
			api_helper::write_comment_count( $data[ 'postID' ], $count );
		}

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
		if ( !defined( 'DOING_AJAX' ) ) define( 'DOING_AJAX', true );
		
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
		if ( !defined( 'DOING_AJAX' ) ) define( 'DOING_AJAX', true );
		
		if (! isset( $data[ 'comment_post_ID' ] ) ) {
			return false;
		}

		$data       = api_helper::pre_validate_comment( $data );

		if ( ! is_array( $data ) ) {
			return false;
		}

		$comment_id = wp_new_comment( $data );

		if ( ! $comment_id )
			return false;

		$comment    = get_comment( $comment_id );
		$approved = $comment->comment_approved;

		if ( 'spam' == $approved )
			return false;

		$comment = (object) api_helper::add_data_to_comment( $comment, ! api_helper::thread() );
		return array(
			'comment_id' => $comment_id,
			'comment'    => $comment,
			'approved'   => $approved,
		);

	}

	/**
	 * Get new comments
	 *
	 * @since 0.0.11
	 *
	 * @param array $data Sanitized data from request
	 *
	 * @return array|bool New comments or false if none found
	 */
	public static function new_comments( $data ) {
		if ( !defined( 'DOING_AJAX' ) ) define( 'DOING_AJAX', true );
		
		if ( 0 == $data[ 'highest' ] ) {
			return false;
		}else{
			$highest = $data[ 'highest' ];
		}

		$args = api_helper::get_comment_args( $data[ 'postID' ] );

		$comments = get_comments( $args );

		if ( is_array( $comments ) && ! empty( $comments ) ) {
			foreach ( $comments as $i => $comment ) {
				if ( $highest >= (int) $comment->comment_ID ) {
					unset( $comments[ $i ] );
				} else {
					$comment        = (array) $comment;
					$comment        = api_helper::add_data_to_comment( $comment, ! api_helper::thread() );
					$comments[ $i ] = $comment;
				}

			}

			$comments = array_values( $comments );
			if ( ! empty( $comments ) && is_array( $comments ) ) {
				$comments = wp_json_encode( $comments );
			} else {
				return false;
			}

		}

		return array(
			'comments' => $comments
		);

	}

}
