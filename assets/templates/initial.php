<?php
/**
 * Template for what is outputted on initial page load
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2015 Transitive, Inc.
 */
use postmatic\epoch\two\epoch;
use postmatic\epoch\two\comments;
use postmatic\epoch\two\front\form;
use postmatic\epoch\two\thread;

$options = epoch::get_instance()->get_options();;
global $post;

$comment_count = comments::get_comment_count( $post->ID );

if ( $comment_count == 0 and ! comments_open( $post ) ) {
	return;
}

$form = sprintf(
	'<div id="epoch-commenting">%2s</div>',
	comments::get_form( $post->ID )
);

$aria = 'aria-live="assertive"';
if ( $options[ 'infinity_scroll' ] ) {
	$aria .= ' aria-atomic="true"';
}

$comment_area = sprintf( '<div id="epoch-comments" %s ><div id="epoch-loading"><div class="dot1"></div><div class="dot2"></div><ol class="comment-list" id="epoch-comment-list"></div></div>', $aria );

if ( $comment_count == 0 ) {
	$comment_count_message = __( 'There are no comments', 'epoch' );
} else {
	$comment_count_message = sprintf(
		_n( 'There is one comment', 'There are %s comments', $comment_count, 'epoch' ),
		'<span id="epoch-count">' . $comment_count . '</span>'
	);
}

if ( 'ASC' == $options['order'] && $comment_count > 3  ) {
	$comment_count_area = sprintf(
		'<h3 class="comment-count-area">%s <a href="#reply-title">%s</a></h3>',
		$comment_count_message,
		esc_html(  $options[ 'before_text' ] )
	);
} else {
	$comment_count_area = sprintf(
		'<h3 class="comment-count-area">%s</h3>',
		$comment_count_message
	);
}

$navigation = comments::navigation();

if ( 'DESC' == $options[ 'order' ] ) {
	$middle = $comment_count_area . $navigation . $form . $comment_area;
}else{
	$middle = $comment_count_area . $comment_area . $navigation . $form;
}

printf(
	'<div id="epoch-wrap" class="comments-area epoch-wrapper">
		%s
	</div>',
	$middle
);

