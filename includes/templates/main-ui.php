<?php
/**
 * The main admin UI
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2015 Transitive, Inc.
 */
?>

<div class="epoch-main-headercaldera">
	<h2>
		<?php _e( 'Epoch', 'epoch' ); ?> <span class="epoch-version"><?php echo EPOCH_VER; ?></span>
		<span style="position: absolute; top: 5px;" id="epoch-save-indicator"><span style="float: none; margin: 10px 0px -5px 10px;" class="spinner"></span></span>
	</h2>
	<div class="updated_notice_box">
		<?php _e( 'Updated Successfully', 'epoch' ); ?>
	</div>
	<div class="error_notice_box">
		<?php _e( 'Could not save changes. Try again.', 'epoch' ); ?>
	</div>

	<ul class="epoch-header-tabs epoch-nav-tabs">
		<li class="{{#is _current_tab value="#epoch-panel-about"}}active {{/is}}epoch-nav-tab">
			<a href="#epoch-panel-about" id="epoch-go-about">
				<?php _e('Welcome', 'epoch') ; ?>
			</a>
		</li>
		<li class="{{#is _current_tab value="#epoch-panel-postmatic"}}active {{/is}}epoch-nav-tab">
			<a href="#epoch-panel-postmatic" id="epoch-go-postmatic">
				<?php _e('Enable free email commenting with Postmatic', 'epoch') ; ?>
			</a>
		</li>
		<li id="postmatic-brand">
			<a href="http://gopostmatic.com" target="_blank"><span>Brought to you by Postmatic</span></a>
		</li>
	</ul>

	<span class="wp-baldrick" id="epoch-field-sync" data-event="refresh" data-target="#epoch-main-canvas" data-callback="epoch_canvas_init" data-type="json" data-request="#epoch-live-config" data-template="#main-ui-template"></span>
</div>

<div class="epoch-sub-headercaldera">
	<ul class="epoch-sub-tabs epoch-nav-tabs">
		<li class="{{#is _current_tab value="#epoch-panel-options"}}active {{/is}}epoch-nav-tab">
			<a href="#epoch-panel-options" id="epoch-go-options">
				<?php _e('Options', 'epoch') ; ?>
			</a>
		</li>
	</ul>
</div>

<form class="caldera-main-form has-sub-nav" id="epoch-main-form" action="?page=epoch" method="POST">
	<?php wp_nonce_field( 'epoch', 'epoch-setup' ); ?>
	<input type="hidden" value="epoch" name="id" id="epoch-id">
	<input type="hidden" value="{{_current_tab}}" name="_current_tab" id="epoch-active-tab">

		<div id="epoch-panel-options" class="epoch-editor-panel" {{#is _current_tab value="#epoch-panel-options"}}{{else}} style="display:none;" {{/is}}>		

		<?php
			//main options
			include EPOCH_PATH . 'includes/templates/options-panel.php';
		?>
	</div>

	<div id="epoch-panel-about" class="epoch-editor-panel" {{#is _current_tab value="#epoch-panel-about"}}{{else}} style="display:none;" {{/is}}>
		<?php
			// about panel
			include EPOCH_PATH . 'includes/templates/about-panel.php';
		?>
	</div>

	<div id="epoch-panel-postmatic" class="epoch-editor-panel" {{#is _current_tab value="#epoch-panel-postmatic"}}{{else}} style="display:none;" {{/is}}>
		<?php
			// postmatic panel
			include EPOCH_PATH . 'includes/templates/postmatic-panel.php';
		?>
	</div>
	<div class="clear"></div>

	<div class="epoch-footer-bar">
		<button type="submit" class="button button-primary wp-baldrick" data-action="epoch_save_config" data-active-class="none" data-callback="epoch_handle_save" data-load-element="#epoch-save-indicator" data-before="epoch_get_config_object" id="epoch-save">
			<?php _e( 'Save Changes', 'epoch' ) ; ?>
		</button>
	</div>	

</form>

{{#unless _current_tab}}
	{{#script}}
		jQuery(function($){
			$('.epoch-nav-tab').first().trigger('click').find('a').trigger('click');
		});
	{{/script}}
{{/unless}}

<script>
	jQuery( '#epoch-go-about, #epoch-go-postmatic' ).click(function() {
		jQuery( '#epoch-save' ).hide();
	});

	jQuery( '#epoch-go-options' ).click(function() {
		jQuery( '#epoch-save' ).show();
	});

</script>
