<?php
/**
 * Given a comment ID, get all ancestors and decendants to create a threaded view with
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2016 Transitive, Inc.
 */
namespace postmatic\epoch\two;


class thread {
	/**
	 * Collected comments
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	protected $comments = array();

	/**
	 * ID of comment we are searching for relatives for
	 *
	 * @since 2.0.0
	 *
	 * @var int
	 */
	protected $comment_id;

	/**
	 * thread constructor.
	 *
	 * @param int $comment_id The ID of any comment in thread
	 */
	public function __construct( $comment_id ) {
		$this->comment_id = $comment_id;
	}

	/**
	 * Collect comments
	 *
	 * @since 2.0.0
	 */
	public function collect() {
		$this->find_ancestors();
		$this->comments[] = get_comment( $this->comment_id );
		$this->comments = array_merge( $this->comments, $this->find_descendants( ) );
	}

	/**
	 * Get collected comments
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_comments() {
		return $this->comments;
	}

	/**
	 * Find all parents, grandparents, etc.
	 *
	 * @since 2.0.0
	 */
	protected function find_ancestors() {
		$comment_id = $this->comment_id;

		while ( $comment_id > 0 ) {
			$comment = get_comment( $comment_id );
			$parent  = $comment->comment_parent;

			if ( $parent ) {
				$this->comments[] = get_comment( $parent );

			}

			$comment_id = $parent;
		}

	}

	/**
	 * Find children, grandchildren etc, recursively
	 *
	 * @since 2.0.0
	 *
	 * @param array $the_children Optional. Default is empty
	 * @param bool|int $comment_id Optional. Default is $this->comment_id
	 *
	 * @return array
	 */
	protected function find_descendants( array $the_children = array(), $comment_id = false ) {
		if ( ! $comment_id ) {
			$comment_id = $this->comment_id;
		}

		$comment  = get_comment( $comment_id );
		$children = $comment->get_children( array(
			'status' => 'approve',
		) );

		if ( $children ) {
			$the_children = array_merge( $the_children, $children );
			foreach ( $children as $child ) {
				$the_children = $this->find_descendants( $the_children, $child->comment_ID );
			}
		}

		return $the_children;

	}

}
