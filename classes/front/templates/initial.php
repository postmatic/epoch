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

$comment_count = get_comment_count( $post->ID );

if ( $comment_count['approved'] == 0 and ! comments_open( $post ) ) {
	return;
}

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

	if ( $comment_count['approved'] == 0 ) {
		$comment_count_message = __( 'There are no comments', 'epoch' );
	} else {
		$comment_count_message = sprintf(
			_n( 'There is one comment', 'There are %s comments', $comment_count['approved'], 'epoch' ),
			'<span id="' . \postmatic\epoch\front\vars::$count_id . '">' . $comment_count['approved'] . '</span>'
		);
	}

	if ( 'ASC' == $options['order'] && $comment_count['approved'] > 3 && 'iframe' != $options['theme'] ) {
		$comment_count_area = sprintf(
			'<h3 class="comment-count-area">%1s <a href="#reply-title">%2s</a></h3>',
			$comment_count_message,
			$options['before_text']
		);
	} else {
		$comment_count_area = sprintf(
			'<h3 class="comment-count-area">%1s</h3>',
			$comment_count_message
		);
	}

	global $wp_query;
	if( 'iframe' == $options[ 'theme' ] && !isset( $wp_query->query['epoch'] ) ){
		$area_before = $area_after = '';
		if ( 'ASC' == $options['order'] && $comment_count['approved'] > 3 ) {
			$area_before = sprintf( '<a href="#epoch-comments-bottom">%s</a>', $options['before_text'] );
			$area_after = '<div id="epoch-comments-bottom" style="margin-top:-330px;height:330px;"></div>';
		}
		$comment_area = sprintf(
		'<iframe id="%1s" src="' . get_permalink( $post->ID ) . 'epoch/" scrolling="no"></iframe>',
		esc_attr( \postmatic\epoch\front\vars::$comments_wrap )
		);
		$comment_area = $area_before . $comment_area . $area_after;
		$comment_count_area = $form = null;
	}

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
