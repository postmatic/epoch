<?php
/**
 * Admin page
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2016 Transitive, Inc.
 */
use postmatic\epoch\two\epoch;

$options = epoch::get_instance()->get_options();
?>
<div id="epoch-admin">
	<div id="epoch-admin-header">
		HEADER - Hi Jason:)
	</div>
	<div id="epoch-saved-good" class="epoch-settings-indicator" style="display: none;visibility: hidden;" aria-hidden="true">
		<p class="notice notice-success">
			<?php esc_html_e( 'Settings Saved Successfully', 'epoch' ); ?>
		</p>
	</div>
	<div id="epoch-saved-bad" class="epoch-settings-indicator" style="display: none;visibility: hidden;" aria-hidden="true">
		<p class="notice notice-error">
			<?php esc_html_e( 'Settings Could Not Be Saved.', 'epoch' ); ?>
		</p>
	</div>
	<form id="epoch-admin-settings">
		<div class="epoch-field-group">
			<label id="epoch-per_page-label" for="epoch-per_page">
				<?php esc_html_e( 'Comments Per Page', 'epoch'  ); ?>
			</label>
			<input type="number" min="1" id="epoch-per_page" value="<?php echo esc_attr( $options[ 'per_page' ] ); ?>" aria-labelledby="epoch-per_page-label">
		</div>

		<div class="epoch-field-group">
			<label id="epoch-order-label" for="epoch-order">
				<?php esc_html_e( 'Comment Order', 'epoch'  ); ?>
			</label>
			<select id="epoch-order" aria-labelledby="epoch-order-label">
				<option value="ASC" <?php if( 'ASC' === $options[ 'order' ] ) { echo 'selected'; } ?> >
					<?php esc_html_e( 'Ascending', 'epoch' ); ?>
				</option>
				<option value="DESC" <?php if( 'DESC' === $options[ 'order' ] ) { echo 'selected'; } ?> >
					<?php esc_html_e( 'Descending', 'epoch' ); ?>
				</option>
			</select>
		</div>

		<div class="epoch-field-group">
			<label id="epoch-before_text-label" for="epoch-before_text">
				<?php esc_html_e( 'Before Text', 'epoch'  ); ?>
			</label>
			<input type="text" id="epoch-before_text" value="<?php echo esc_attr( $options[ 'before_text' ] ); ?>" aria-labelledby="epoch-epoch-before_text-label" />
		</div>

		<div class="epoch-field-group">
			<label id="epoch-infinity_scroll-label" for="epoch-infinity_scroll">
				<?php esc_html_e( 'Enable Infinite Scroll?', 'epoch'  ); ?>
			</label>
			<input type="checkbox" id="epoch-infinity_scroll" <?php if( true == $options[ 'infinity_scroll' ] ) { echo 'checked'; } ?> aria-labelledby="epoch-infinity_scroll-label" />
		</div>

		<?php echo wp_nonce_field( 'epoch-admin' ); ?>
		<div class="epoch-field-group">
			<label id="epoch-submit-label" for="epoch-submit" class="screen-reader-text">
				<?php esc_html_e( 'Save Settings', 'epoch'  ); ?>
			</label>
			<input type="submit" id="epoch-submit" class="button button-primary" aria-labelledby="epoch-submit-label" value="<?php esc_attr_e( 'Save', 'epoch' ); ?>" />
			<span id="epoch-spinner" aria-hidden="true" class="spinner"></span>
		</div>

	</form>
	<div id="epoch-admin-sidebar">
		SIDEBAR
	</div>
</div>
