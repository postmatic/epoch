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


$comment_area = sprintf(
	'<div id="%1s"></div>',
	esc_attr( \postmatic\epoch\front\vars::$comments_wrap )
);


printf(
	'<div id="%1s" class="comments-area %2s">

	</div>',
	esc_attr( \postmatic\epoch\front\vars::$wrap_id ),
	esc_attr( \postmatic\epoch\front\vars::$wrap_class ),
	$comment_area
);
