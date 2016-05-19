<?php
/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace postmatic\epoch\two;


class thread {
	protected $comments = array();

	protected $comment_id;
	public function __construct( $comment_id ){
		$this->comment_id = $comment_id;
	}

	public function collect(){
		$this->find_ancestors();
		$this->comments[] = get_comment( $this->comment_id );
		$this->comments = array_merge( $this->comments, $this->find_descendants( ) );
	}

	public function get_comments(){
		return $this->comments;
	}


	protected function find_ancestors( ) {
		$comment_id = $this->comment_id;
		
		while( $comment_id > 0 ) {
			$comment = get_comment( $comment_id );
			$parent = $comment->comment_parent;
			if ( $parent ) {
				$this->comments[] = get_comment( $parent );
			
			}
			$comment_id = $parent;



		}

	}

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
			foreach( $children as $child ){
				$the_children = $this->find_descendants( $the_children, $child->comment_ID );
			}
		}

		return $the_children;

	}
}
