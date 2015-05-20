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
	<input id="epoch-options-theme-dark" type="radio" name="options[theme]" value="dark" {{#is options/theme value="dark"}}checked{{/is}}><?php _e( 'Dark (coming soon)', 'epoch' ); ?>
	<input id="epoch-options-theme-light" type="radio" name="options[theme]" value="light" {{#is options/theme value="light"}}checked{{/is}}><?php _e( 'Light', 'epoch' ); ?>
	<input id="epoch-options-theme-none" type="radio" name="options[theme]" value="none" {{#is options/theme value="none"}}checked{{/is}}><?php _e( 'None (uses the style that came with your theme - results may vary)', 'epoch' ); ?>
	<p class="description" style="margin-left: 190px;">
		<?php _e( 'Epoch comes with a light theme and a dark theme for displaying your comments. Choose whichever works best for your site. The comment template can also be styled easily via css. See the documentation.', 'epoch' ); ?>
	</p>
	<p class="description" style="margin-left: 190px;">
		<?php _e( 'Choose "none" to rely only on your theme\'s style or add your own.', 'epoch' ); ?>
	</p>
</div>

<div class="epoch-config-group">
	<label for="epoch-options-interval">
		<?php _e( 'Comment Check Interval', 'epoch' ); ?>
	</label>
	<input id="epoch-options-interval" type="number" name="options[interval]" value="{{options/interval}}" >
	<p class="description" style="margin-left: 190px;">
		<?php _e( 'How frequently should Epoch push new comments to active users (in seconds)? ', 'epoch' ); ?>
		<?php _e( 'Comments are only pushed to users viewing the comments area of a post in an active browser tab. It is highly efficient. ', 'epoch' ); ?>
		<?php _e( 'If no value set, interval will be 15 seconds, which should be fine on most hosts.', 'epoch' ); ?>
	</p>
</div>

<div class="epoch-config-group">
	<label for="epoch-options-before_text">
		<?php _e( 'Headline', 'epoch' ); ?>
	</label>
	<input id="epoch-options-before_text" type="text" class="regular-text" name="options[before_text]" value="{{options/before_text}}" >
	<p class="description" style="margin-left: 190px;">
		<?php _e( 'Text to show before the comment form.', 'epoch' ); ?>
	</p>
</div>

<div class="epoch-config-group">
	<label for="epoch-options-order">
		<?php _e( 'Comment Order', 'epoch' ); ?>
	</label>
	<select id="epoch-options-order" name="options[order]" value="{{options/before_text}}" >
		<option value="ASC" {{#is options/order value="ASC"}}selected{{/is}} >
			<?php _e( 'Ascending', 'epoch' ); ?>
		</option>
		<option value="DESC" {{#is options/order value="DESC"}}selected{{/is}} >
			<?php _e( 'Descending', 'epoch' ); ?>
		</option>
	</select>
	<p class="description" style="margin-left: 190px;">
		<?php _e( 'Should comments be shown in ascending or descending order? Descending puts the latest comment and comment form at the top of the comments area. Ascending does the opposite.', 'epoch' ); ?>
	</p>
</div>

