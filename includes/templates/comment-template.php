<?php
/**
 * Single post (not download or free_plugin) view
 *
 * @package   epock comment theme
 * @author    David Cramer <david@calderawp.com>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2015 David Cramer
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
	wp_footer();
	?>
	<script>

	jQuery( function( $ ){
		var frame = $( window.parent.document.getElementById( 'epoch-comments' ) );
		frame.css({ width: '100%', overflow: 'hidden'});
		setInterval( function(){
			frame.height( $( document ).outerHeight() );
		}, 100 );

	} );
		 

	</script
	</body>
</body>