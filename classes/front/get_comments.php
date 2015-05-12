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


use postmatic\epoch\options;

class get_comments {


	/**
	 * The comments we found.
	 *
	 * @since 0.0.5
	 *
	 * @var array
	 */
	public $comments;

	/**
	 * Comments we found, with each one converted to an array.
	 *
	 * @since 0.0.6
	 *
	 * @access protected
	 *
	 * @var object
	 */
	protected $comments_as_arrays;

	/**
	 * Sorting array for finding parents of comments
	 *
	 * @since 0.0.6
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $sorter;

	/**
	 * Indexing array for finding a value from $this->sorter in $this->comments_as_arrays
	 *
	 * @since 0.0.6
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $indexer;


	/**
	 * Constructor for class
	 *
	 * @since 0.0.4
	 *
	 * @param int $post_id Post ID
	 */
	public function __construct( $post_id ) {
		$args = api_helper::get_comment_args( $post_id );


		$this->comments = get_comments( $args  );

		//switch all comments to arrays
		$this->comments_as_arrays =  $this->to_arrays( $this->comments );

		if ( is_array( $this->comments ) && ! empty( $this->comments ) ) {
			if ( api_helper::thread() ) {
				$this->prepare_sort();
				$this->sort();

			}

		}

		//switch comments back to objects
		$this->comments = $this->to_objects( $this->comments_as_arrays );

	}

	/**
	 * Prepares class properties we need for sorting
	 *
	 * @since 0.0.6
	 *
	 * @access protected
	 */
	protected function prepare_sort() {
		//make an array of comment_id => comment_parent
		$this->sorter = array_combine( wp_list_pluck( $this->comments, 'comment_ID' ), wp_list_pluck( $this->comments, 'comment_parent') );

		//make index array for sorter array
		$this->indexer = $indexer = array_combine( array_keys( (array) $this->comments ), wp_list_pluck( $this->comments, 'comment_ID' ) );

	}

	/**
	 * Sort comments for proper threading
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 */
	protected function sort() {
		$sorter = $this->sorter;
		$indexer = $this->indexer;
		$this->comments_as_arrays;

		//add extra fields to responses
		foreach( $this->comments_as_arrays as $i => $comment ) {
			$this->comments_as_arrays[ $i ] = api_helper::add_data_to_comment( $comment );

		}

		//add children to their parents
		foreach( $sorter as $id => $parent_id ) {
			if ( $parent_id ) {
				$key     = array_search( $id, $indexer );
				$comment = $this->comments_as_arrays[ $key ];
				$parent_key = array_search( $parent_id, $indexer );
				$comment[ 'depth' ] = $this->find_depth( $comment );
				$parent = $this->comments_as_arrays[ $parent_key ];
				$parent[ 'children' ][] = $comment;
				$this->comments_as_arrays[ $parent_key ] = $parent;

			}

		}

		//unset any comment that has a parent from top level array.
		foreach( $sorter as $id => $parent ) {
			if ( $parent ) {
				$key     = array_search( $id, $indexer );
				unset( $this->comments_as_arrays[ $key ] );

			}

		}

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

	/**
	 * A comment to find depth of
	 *
	 * @since 0.0.6
	 *
	 * @access protected
	 *
	 * @param array $comment a comment
	 *
	 * @return int The depth
	 */
	protected function find_depth( $comment ) {
		$depth = 2;
		$max_depth =  get_option( 'thread_comments_depth', 5 );
		$parent_id = $comment[ 'comment_parent'];
		$parent = $this->find_parent($parent_id );
		for( $i = 0; $i <= $max_depth; $i++ ) {
			if (  0 == $parent[ 'comment_parent' ] ) {
				break;
			}else{
				$parent = $this->find_parent( $parent[ 'comment_parent' ] );
				$depth++;
				if ( is_array( $parent ) ) {
					break;
				}

			}

		}

		return $depth;

	}

	/**
	 * Find parent of a comment in $this->comments_as_arrays
	 *
	 * @since 0.0.6
	 *
	 * @access protected
	 *
	 * @param int|string $parent_id Parent ID
	 *
	 * @return array|void Comment as an array or void, if not found.
	 */
	protected function find_parent( $parent_id ) {
		$parent_key = array_search( $parent_id, $this->indexer );
		if ( isset( $this->comments_as_arrays[ $parent_key ] ) ) {
			$parent = $this->comments_as_arrays[ $parent_key ];

			return $parent;
		}

	}



}
