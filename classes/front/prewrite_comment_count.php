<?php
/**
 * Pre-prime our text file comment count cache
 *
 * @package   epoch
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace postmatic\epoch\front;


class prewrite_comment_count {

	/**
	 * Actions to hook to 'update_count'
	 *
	 * @since 0.0.8
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $actions = array(
		'unspam_comment',
		'comment_post',
		'edit_comment',
		'trash_comment',
		'delete_comment',
		'untrashed_comment',
		'spammed_comment'
	);

	/**
	 * Setup our hooks
	 *
	 * @since 1.1.8
	 */
	public function __construct() {
		foreach ( $this->actions as $action  ) {
			add_action( $action, array( $this, 'update_count' ) );
		}

		add_action( 'transition_comment_status', array( $this, 'transition_status' ), 10, 3 );
		add_action( 'save_post', array( $this, 'save_post' ) );

	}

	/**
	 * Callback for comment hooks
	 *
	 * @since 0.0.8
	 *
	 * @param int $comment_id
	 */
	public function update_count( $comment_id ) {
		$this->unhook();
		$post_id = $this->find_post_id( $comment_id );
		$this->maybe_update_count( $post_id, $this->get_comment_count( $post_id ) );
	}

	/**
	 * Callback for "transition_comment_status"
	 *
	 * Deal with the fact that this hook does not pass comment ID like the others do.
	 *
	 * @param $new_status
	 * @param $old_status
	 * @param $comment
	 */
	public function transition_status( $new_status, $old_status, $comment ) {
		$comment_id = $comment->comment_ID;
		$this->update_count( $comment_id );
	}


	/**
	 * Make sure it is there on save post too
	 *
	 * @since 0.0.8
	 *
	 * @param $post_id
	 */
	public function save_post( $post_id ){
		$this->unhook();
		$this->maybe_update_count( $post_id, $this->get_comment_count( $post_id ) );
	}

	/**
	 * Remove all of our actions
	 *
	 * @since 0.0.8
	 */
	protected function unhook() {
		foreach ( $this->actions as $action  ) {
			remove_action( $action, array( $this, 'update_count' ) );
		}

		remove_action( 'save_post', array( $this, 'save_post' ) );
		remove_action( 'transition_comment_status', array( $this, 'transition_status' ) );
	}


	/**
	 * Check if we need to update, and if needed, make it so.
	 *
	 * @since 0.0.8
	 *
	 * @access protected
	 *
	 * @param $post_id
	 * @param $count
	 */
	protected function maybe_update_count( $post_id, $count ) {
		$current = $this->check_written_count( $post_id );
		if( $current != $count ) {
			api_helper::write_comment_count( $post_id, $count );
		}


	}

	/**
	 * Get count of approved comments
	 *
	 * @since 0.0.8
	 *
	 * @access protected
	 *
	 * @param $post_id
	 *
	 * @return int
	 */
	protected function get_comment_count($post_id ) {
		$count = get_comment_count( $post_id );
		return $count[ 'approved' ];
	}

	/**
	 * Get comment count from file cache
	 *
	 * @since 0.0.8
	 *
	 * @access protected
	 *
	 * @param $post_id
	 *
	 * @return bool|int Number if file exists and false if it doesn't exist
	 */
 	protected function check_written_count( $post_id ) {
		$path = api_paths::comment_count_alt_check_url( $post_id, false );
		if( file_exists( $path ) ) {
			$count = file_get_contents( $path );
			return absint( $count );
		}

		return false;
	}

	/**
	 * Get the post ID by comment ID
	 *
	 * @since 0.0.8
	 *
	 * @access protected
	 *
	 * @param int $comment_id
	 *
	 * @return int post id
	 */
	protected function find_post_id( $comment_id ) {
		$comment = get_comment( $comment_id );
		$post_id = $comment->comment_post_ID;
		return $post_id;
	}

}
