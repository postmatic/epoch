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

$form = sprintf(
	'<div id="%1s">%2s</div>',
	esc_attr( \postmatic\epoch\front\vars::$form_wrap ),
	\postmatic\epoch\front\layout::spinner_img_tag( \postmatic\epoch\front\vars::$comment_form_spinner_id )
);

$comment_area = sprintf(
	'<div id="%1s">%2s</div>',
	esc_attr( \postmatic\epoch\front\vars::$comments_wrap ),
	\postmatic\epoch\front\layout::spinner_img_tag( \postmatic\epoch\front\vars::$comments_area_spinner_id )
);

if ( 'DESC' == $options[ 'order' ] ) {
	$middle = $form . $comment_area;
}else{
	$middle = $comment_area . $form;    
}

printf(
	'<div id="%1s" class="comments-area %2s">
		%3s
	</div>',
	esc_attr( \postmatic\epoch\front\vars::$wrap_id ),
	esc_attr( \postmatic\epoch\front\vars::$wrap_class ),
	$middle
);
