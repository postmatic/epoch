<?php
/**
 * Load the admin UI
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2015 Transitive, Inc.
 */
$epoch = \postmatic\epoch\options::get();

?>
<div class="wrap" id="epoch-main-canvas">
	<span class="wp-baldrick spinner" style="float: none; display: block;" data-target="#epoch-main-canvas" data-callback="epoch_canvas_init" data-type="json" data-request="#epoch-live-config" data-event="click" data-template="#main-ui-template" data-autoload="true"></span>
</div>

<div class="clear"></div>

<input type="hidden" class="clear" autocomplete="off" id="epoch-live-config" style="width:100%;" value="<?php echo esc_attr( json_encode($epoch) ); ?>">

<script type="text/html" id="main-ui-template">
	<?php
		// pull in the rest of the admin
		include EPOCH_PATH . 'includes/templates/main-ui.php';
	?>	
</script>





