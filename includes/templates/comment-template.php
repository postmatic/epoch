<?php
/**
 * Tempalte for inside of iFrame
 *
 * @package   epoch
 * @author    David Cramer <david@calderawp.com>
 * @license   GPL-2.0+
 */
?>
<!DOCTYPE html>

<html <?php language_attributes(); ?>>

	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">

		<?php if ( is_singular() ) wp_enqueue_script( "comment-reply" ); ?>
		<link rel="stylesheet" href="<?php echo EPOCH_URL; ?>assets/css/front/iframe.css" type="text/css" media="all" />
	</head>
	
	<body style="padding:0; margin:0;">
	<?php
		comments_template( '', true );
		/**
		 * Runs in footer of the iFrame template
		 *
		 * @since 1.0.0
		 */
		do_action( 'epoch_iframe_footer' );
	?>
	<script>

	jQuery( function( $ ){
		var frame = $( window.parent.document.getElementById( 'epoch-comments' ) );
		frame.css({ width: '100%', overflow: 'hidden'});
		setInterval( function(){
			frame.height( $( document ).outerHeight() );
			$('a:not([href="#reply-title"]').attr('target', '_parent');
		}, 100 );

	} );
		 

	</script>	
	</body>
</body>
