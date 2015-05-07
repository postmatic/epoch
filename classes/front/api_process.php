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
	 * Get comment form HTML
	 *
	 * @since 0.0.1
	 *
	 * @param array $data Sanitized data from request
	 *
	 * @return array
	 */
	public static function form( $data ) {
		$options = options::get();
		$options = $options[ 'options' ];
		$args = array(
			'id_form' => vars::$form_id,
			'id_submit ' => vars::$submit_id,
		);

		if ( isset( $options[ 'before_text' ] ) && is_string( $options[ 'before_text' ] ) ) {
			$args[ 'title_reply' ] = $options[ 'before_text' ];
		}

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

		$not_in = null;
		if ( isset( $data[ 'ignore' ] ) ) {
			$not_in = $data[ 'ignore' ];
		}

		$comments = new get_comments( $data[ 'postID' ], $not_in );
		$comments = array_values( $comments->comments );
		if ( ! empty( $comments ) && is_array( $comments ) ) {

			$comments = api_helper::improve_comment_response( $comments );

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

		$data       = api_helper::pre_validate_comment( $data );
		$data       = wp_filter_comment( $data );
		if ( is_array( $data ) ) {
			$comment_id = wp_insert_comment( $data );

			if ( $comment_id ) {
				$comment    = get_comment( $comment_id );
				$comment = (object)
api_helper::add_data_to_comment( $comment );
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
	 * Get a single comment
	 *
	 * @since 0.0.5
	 *
	 * @param array $data
	 *
	 * @return array|null
	 */
	public static function get_comment( $data ) {
		$comment = get_comment( $data[ 'commentID' ] );

		if ( is_object( $comment ) ) {
			$comment = api_helper::add_data_to_comment( $comment );
			return array(
				'comment' => $comment
			);

		}

	}


}
