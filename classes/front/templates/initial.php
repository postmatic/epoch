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
printf(
	'<div class="comments-area %1s">
		<div id="%2s">%3s</div>
		<div id="%4s">%5s</div>
	</div>',
	esc_attr( \postmatic\epoch\front\vars::$wrap_class ),
	esc_attr( \postmatic\epoch\front\vars::$form_wrap ),
	\postmatic\epoch\front\layout::spinner_img_tag( \postmatic\epoch\front\vars::$comment_form_spinner_id ),
	esc_attr( \postmatic\epoch\front\vars::$comments_wrap ),
	\postmatic\epoch\front\layout::spinner_img_tag( \postmatic\epoch\front\vars::$comments_area_spinner_id )
);
