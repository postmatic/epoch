<?php
/**
 * Get comments and sort as needed.
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2015 Transitive, Inc.
 */
namespace postmatic\epoch\front;


class get_comments {


	/**
	 * The comments we found.
	 *
	 * @since 0.0.4
	 *
	 * @var object
	 */
	public $comments;

	/**
	 * Constructor for class
	 *
	 * @since 0.0.4
	 *
	 * @param int $post_id Post ID
	 * @param null|array $not_in Optional. If is nto null, the default, should be an array of comment IDs to ignore.
	 */
	public function __construct( $post_id, $not_in = null ) {
		$args = array(
			'post_id' => $post_id
		);
		if ( $not_in ) {
			$args[ 'comment__not_in' ] = $not_in;
		}

		$this->comments = $comments =  get_comments( $args  );
		if (  get_option( 'thread_comments' ) ) {
			if ( is_array( $comments ) && ! empty( $comments ) ) {
				$this->sort();
			}

		}

	}

	/**
	 * Thread comments
	 *
	 * @since 0.0.4
	 */
	protected function sort() {
		//make an array of comment_id => comment_parent
		$sorter = array_combine( wp_list_pluck( $this->comments, 'comment_ID' ), wp_list_pluck( $this->comments, 'comment_parent') );
		$indexer = array_combine( array_keys( (array) $this->comments ), wp_list_pluck( $this->comments, 'comment_ID' ) );
		$comments = $this->to_arrays( $this->comments );
		foreach( $sorter as $id => $parent ) {
			if ( $parent ) {
				$key     = array_search( $id, $indexer );
				$comment = $comments[ $key ];
				$parent_key = array_search( $parent, $indexer );
				$parent = $comments[ $parent_key ];
				if ( ! isset( $parent[ 'children' ] ) ) {
					$parent[ 'children' ] = array();
				}

				//add extra fields to response
				$comment = api_process::add_data_to_comment( $comment );

				$parent[ 'children' ][] = $comment;
				$comments[ $parent_key ] = $parent;

			}

		}

		foreach( $sorter as $id => $parent ) {
			if ( $parent ) {
				$key     = array_search( $id, $indexer );
				unset( $comments[ $key ] );

			}

		}

		$this->comments = $this->to_objects( $comments );

	}

	/**
	 * Cast all comments to objects
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @param array $comments
	 *
	 * @return array
	 */
	protected function to_objects( $comments ) {
		foreach( $comments as $i => $comment ) {
			$comment = (object) $comment;
			$comments[ $i ] = $comment;
		}

		return $comments;

	}

	/**
	 * Cast all comments to arrays
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @param array $comments
	 *
	 * @return array
	 */
	protected function to_arrays( $comments ) {
		foreach( $comments as $i => $comment ) {
			$comment = (array) $comment;
			$comments[ $i ] = $comment;
		}

		return $comments;

	}

}
