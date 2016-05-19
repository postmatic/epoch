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

	public static function navigation(){
		return sprintf( '<nav class="navigation comment-navigation" role="navigation">
				<h2 class="screen-reader-text">%s</h2><div class="nav-links"><div class="nav-previous"><a href="#epoch-comments" id="epoch-prev">%s</a></div><div class="nav-next"><a href="#epoch-comments" id="epoch-next">%s</a></div></div><!-- .nav-links --></nav><!-- .comment-navigation -->',
			__( 'Comment navigation', 'epoch' ),
			__( 'Older Comments', 'epoch' ),
			__( 'Newer Comments', 'epoch' )
		);

	}

	/**
	 * Get comment form HTML
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id Post ID for this comment form
	 *
	 * @return string
	 */
	public static function get_form( $post_id ) {
		if ( 0 < absint( $post_id ) && comments_open( $post_id ) ) {
			$options = epoch::get_instance()->get_options();

			$args = array(
				'id_form'             => 'commentform',
				'id_submit '          => 'epoch-submit',
				'comment_notes_after' => '',
			);

			$args['title_reply'] = $options['before_text'];

			ob_start();
			comment_form( $args, $post_id );
			$html = ob_get_clean();
		} else if ( ! comments_open( $post_id ) ) {
			$html = __( 'Comments are closed.', 'epoch' );
		} else {
			$html = '';
		}

		return $html;

	}
}
