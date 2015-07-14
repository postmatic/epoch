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
	
	</head>
	
	<body>
	<?php
	comments_template( '', true );
	wp_footer();
	?>
	</body>
</body>