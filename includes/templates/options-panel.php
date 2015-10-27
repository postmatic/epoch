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
		<?php _e( 'How agressively should Epoch override your default comment template?', 'epoch' ); ?>
	</label>
	<input id="epoch-options-theme-iframe" type="radio" name="options[theme]" value="iframe" {{#is options/theme value="iframe"}}checked{{/is}}><?php _e( 'Completely (beta)', 'epoch' ); ?>
	<input id="epoch-options-theme-light" type="radio" name="options[theme]" value="light" {{#is options/theme value="light"}}checked{{/is}}><?php _e( 'Use my typography and colors', 'epoch' ); ?>
	<input id="epoch-options-theme-none" type="radio" name="options[theme]" value="none" {{#is options/theme value="none"}}checked{{/is}}><?php _e( 'Minimally', 'epoch' ); ?>
	<p class="description" style="margin-left: 190px;">
		<?php _e( 'Epoch can integrate with your theme in three different. Give each a try and see what works for you. The comment template can also be styled easily via css. See the documentation.', 'epoch' ); ?>
	</p>
	<p class="description" style="margin-left: 190px;"><strong>Override Completely</strong>: Entirely replace the comment template which came with your theme. Much like Disqus or Jetpack Commenting. Works in all themes. <span style="color:#c0392b">Beta:</span> This method is great for ensuring functionality in all themes but is not compatible with many 3rd party commenting plugins, even the ones we recommend. In particular WP Markdown and WP Social Login will not work with this method. We'll have that resolved soon.
	</p>
	<p class="description" style="margin-left: 190px;"><strong>Use my typography and colors </strong>: Override the comment template which came with your theme while still using colors, typography settings, and branding that you have in place. Works well in most themes.
	</p>
	<p class="description" style="margin-left: 190px;"><strong>Minimally</strong>: Attempt to use the comment style that came with your theme. This approach is highly unstable and depends on the coding practices of your theme developer. Try it. Maybe it'll work! If not, try one of the other settings.
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

<div class="epoch-config-group">
	<label for="epoch-options-order">
		<?php _e( 'Pings & Trackbacks', 'epoch' ); ?>
	</label>
	<input type="checkbox" name="options[show_pings]" value="true" {{#if options/show_pings}}checked{{/if}} /> <?php _e( 'Show pings and trackbacks', 'epoch' ); ?>
	<p class="description" style="margin-left: 190px;">
		<?php _e( 'When enabled, pings and trackbacks will show in the comment stream.', 'epoch' ); ?>
	</p>
</div>
