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
    
    <div id="epoch-loading">
      <div class="dot1"></div>
      <div class="dot2"></div>
    </div>

	<script>

    jQuery( function( $ ){
        var frame = $( window.parent.document.getElementById( 'epoch-comments' ) );
        frame.css({ width: '100%', overflow: 'hidden'});
        scroll = true;
        comment_top = 0;
        setInterval( function(){
            frame.height( $( document ).outerHeight() );
            if ( frame.children().length > 0 && scroll == true ) {
                var parent_location = "" + window.parent.location;
                var pattern = /(#comment-\d+)/;
                if ( pattern.test( parent_location ) ) {
                    comment_location = jQuery( "" + window.parent.location.hash );	                
                    if ( comment_location.length > 0 ) {
                        var targetOffset = comment_location.offset().top;
                        if ( targetOffset == 0 ) {
                            return;   
                        } else {
                            if ( targetOffset == comment_top ) {
                                scroll = false;
                                final_offset = targetOffset + frame.offset().top;
                                jQuery( window.parent.document ).find( 'html,body' ).animate( {scrollTop: final_offset}, 1 );
                            } else {
                                comment_top = targetOffset;    
                            }
                         	  
                        }
                        
                    }
                }
            }
        	$('a:not([href="#reply-title"]').attr('target', '_parent');
        }, 100 );



        $('iframe#epoch-comments').ready(function() {
            document.addEventListener('epoch_loaded', function (e) {
                var loading = document.getElementById( epoch_vars.loading );
                $( loading ).children().hide().attr( 'aria-hidden', 'true' ).remove();
                $( loading ).hide().attr( 'aria-hidden', 'true' ).remove();
                $( '#epoch-loading' ).slideUp();
            }, false);
        });


    } );
		 

	</script>	
	</body>
</body>
