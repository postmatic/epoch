<?php
/**
 * Utility functions for working with comments
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2016 Transitive, Inc.
 */

namespace postmatic\epoch\two;

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
		$count    = 0;
		$comments = get_approved_comments( $post_id );

		foreach ( $comments as $comment ) {

			if ( $comment->comment_type === '' || ( $comment->comment_type === 'pingback' && empty( $options['hide_pings'] ) ) ) {
				$count++;
			}

		}

		return (int) $count;

	}

	/**
	 * Get paged comments for a post
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id
	 * @param int $page
	 * @param int|bool $number Optional. Number of comments to get, default is the per_page option.
	 * @param int|bool $offset Optional. Offset for comment query. If false it will be calculated based on page.
	 *
	 * @return array|int
	 */
	public static function get_comments( $post_id, $page, $number = false, $offset = false ) {
		$options = epoch::get_instance()->get_options();
		$per_page = $options['per_page'];

		if ( ! is_numeric( $number ) ) {
			$number = $per_page;
		}

		if ( ! is_numeric( $offset ) ) {
			$offset = $per_page * ( $page -1 );
		}

		$args = [
			'post_id' => $post_id,
			'number'  => $number,
			'offset'  => $offset,
		];

		if ( ! current_user_can( 'moderate_comments' ) ) {
			$args['status'] = 'approve';
			$args['include_unapproved'] = false;
		} else {
			$args['include_unapproved'] = true;
		}

		$comments = get_comments( $args );

		return $comments;
	}


	/**
	 * Create HTML for comment navigation
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function navigation() {
		$nav = sprintf( '<nav id="epoch-navigation" class="epoch-navigation epoch-comment-navigation" role="navigation">
				<h2 class="screen-reader-text">%s</h2><div class="epoch-nav-links"><div class="epoch-nav-previous"><a href="#epoch-comments" id="epoch-prev">%s</a></div><div class="epoch-nav-next"><a href="#epoch-comments" id="epoch-next">%s</a></div></div><!-- .epoch-nav-links --></nav><!-- .comment-navigation -->',
			esc_html__( 'Comment navigation', 'epoch' ),
			esc_html__( 'Show Newer Comments', 'epoch' ),
			esc_html__( 'Show Older Comments', 'epoch' )
		);

		$nav .= sprintf( '<div id="epoch-load-all" class="epoch-hide" aria-hidden="true"><a href="#epoch-commenting" title="%s" class="button"> %s</a></div>',
			esc_attr__( 'Click to see all comments', 'epoch' ),
			esc_html__( 'Show All Comments', 'epoch' )
		);

		$nav .= '<div id="epoch-infinity-spinner" class="spinner epoch-hide" aria-hidden="true"></div>';

		return $nav;
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
		} elseif ( ! comments_open( $post_id ) ) {
			$html = __( 'Comments are closed.', 'epoch' );
		} else {
			$html = '';
		}

		return $html;

	}

}
