<?php
/**
 * The options panel in admin
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2015 Transitive, Inc.
 */
?>
<div class="epoch-config-group">
	<label for="epoch-options-theme">
		<?php _e( 'Theme', 'epoch' ); ?>
	</label>
	<input id="epoch-options-theme-dark" type="radio" name="options[theme]" value="dark" {{#is options/theme value="dark"}}checked{{/is}}><?php _e( 'Dark', 'epoch' ); ?>
	<input id="epoch-options-theme-light" type="radio" name="options[theme]" value="light" {{#is options/theme value="light"}}checked{{/is}}><?php _e( 'Light', 'epoch' ); ?>
	<p class="description" style="margin-left: 190px;">
		<?php _e( 'Choose theme for comment display.', 'epoch' ); ?>
	</p>
</div>

<div class="epoch-config-group">
	<label for="epoch-options-interval">
		<?php _e( 'Comment Check Interval', 'epoch' ); ?>
	</label>
	<input id="epoch-options-interval" type="number" name="options[interval]" value="{{options/interval}}" >
	<p class="description" style="margin-left: 190px;">
		<?php _e( 'How long to wait before checking for new comments (in seconds).', 'epoch' ); ?>
		<?php _e( 'If no value set, interval will be 15 seconds.', 'epoch' ); ?>
	</p>
</div>


<div class="epoch-config-group">
	<label for="epoch-options-before_text">
		<?php _e( 'Headline', 'epoch' ); ?>
	</label>
	<input id="epoch-options-before_text" type="text" class="regular-text" name="options[before_text]" value="{{options/before_text}}" >
	<p class="description" style="margin-left: 190px;">
		<?php _e( 'Text to show before comments', 'epoch' ); ?>
	</p>
</div>
