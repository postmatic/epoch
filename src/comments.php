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
use postmatic\epoch\two\epoch;

class comments {

	/**
	 * Given a post ID determine the number of comments
	 *
	 * @since  2.0.0
	 *
	 * @param  int $post_id The post ID
	 * @return int          The comment count
	 */
	public static function get_comment_count( $post_id ) {

		$count   = 0;

		$comments = get_approved_comments( $post_id );

		foreach ( $comments as $comment ) {

			if ( $comment->comment_type === '' || ( $comment->comment_type === 'pingback' && empty( $options['hide_pings'] ) ) ) {
				$count++;
			}

		}

		return (int) $count;

	}

	public static function get_comments( $post_id, $page ){
		$options = epoch::get_instance()->get_options();

		$per_page = $options[ 'per_page' ];
		$offset = $per_page * ( $page -1 );
		$comments = get_comments( [
			'post_id' => $post_id,
			'number'  => $per_page,
			'offset'  => $offset
		] );

		return $comments;

	}
}
