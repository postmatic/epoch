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
$options = \postmatic\epoch\options::get_display_options();
global $post;
$form = sprintf(
	'<div id="%1s">%2s</div>',
	esc_attr( \postmatic\epoch\front\vars::$form_wrap ),
	\postmatic\epoch\front\layout::get_form( $post->ID )
);

$comment_area = sprintf(
	'<div id="%1s"></div>',
	esc_attr( \postmatic\epoch\front\vars::$comments_wrap )
);

if ( 'none' == $options[ 'theme' ] ) {
	$comment_count_area = '';
}else{
	$comment_count = sprintf(
		__( 'There are <span id="%s">0</span> comments. <a href="#reply-title">DYLAN INSERT http://note.io/1Q08VIT here</a>.', 'epoch' ),
		esc_attr( \postmatic\epoch\front\vars::$count_id )
	);

	$comment_count_area = sprintf(
		'<h3 class="comment-count-area">%s</h3>',
		$comment_count
	);
}

if ( 'DESC' == $options[ 'order' ] ) {
	$middle = $comment_count_area . $form . $comment_area;
}else{
	$middle = $comment_count_area . $comment_area . $form;
}

if ( 'none' != $options[ 'theme' ] ) {
	echo '<div id="comments"></div>';
}

printf(
	'<div id="%1s" class="comments-area %2s">
		%3s
	</div>',
	esc_attr( \postmatic\epoch\front\vars::$wrap_id ),
	esc_attr( \postmatic\epoch\front\vars::$wrap_class ),
	$middle
);
