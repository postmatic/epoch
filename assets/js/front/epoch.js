/** globals epoch_vars */
jQuery( document ).ready( function ( $ ) {

    (function ( $, app ) {
        /**
         * Bootstrap
         *
         * @since 0.0.1
         */
        app.init = function() {


            //element for comments area
            app.comments_wrap_el = document.getElementById( epoch_vars.comments_wrap );

            //element for template
            app.template_el = document.getElementById( epoch_vars.comments_template_id );


            /**
             * Start it up!
             */
            app.set_width();
            document.body.onload = app.add_iframe();

            window.onresize = function(event) {
               app.set_width();
            };


        };

        /**
         * Resize the epoch container based on content width
         *
         * @since 0.0.6
         */
        app.set_width = function() {
            el = document.getElementById( epoch_vars.sniffer );

            if ( null != el ) {
                content_width = $( el ).parent().outerWidth();
                if ( 'number' == typeof content_width ) {
                    wrap_el = document.getElementById( epoch_vars.wrap_id );
                    $( wrap_el ).css( 'width', content_width );
                }

            }

        };


        /**
         * Add our iFrame
         *
         * @since 0.4.0
         */
        app.add_iframe = function() {
            //create iFrame
            app.comment_iframe_el = document.createElement('iframe');
            app.comment_iframe_el.id = 'epoch-comment-iframe';
            app.comment_inner_wrap_el = document.createElement('div');
            app.comment_inner_wrap_el.id = 'epoch-comment-inner-wrap';

            //append iFrame to DOM
            $( app.comment_iframe_el ).appendTo(  app.comments_wrap_el );

            //add the element inside the iFrame to put comments in
            $( app.comment_iframe_el ).contents().find( 'body' ).append( app.comment_inner_wrap_el );



            //add JS inside
            var script = document.createElement( 'script') ;
            script.setAttribute('src', epoch_vars.iframe_js );
            script.setAttribute('type', 'text/javascript' );
            $( app.comment_iframe_el ).contents().find('head').append( script );

            //add CSS inside
            var style = document.createElement( 'link' );
            style.setAttribute( 'href', epoch_vars.iframe_css );
            style.setAttribute( 'rel', 'stylesheet' );
            style.setAttribute( 'type', 'text/css' );
            $( app.comment_iframe_el ).contents().find('head').append( style );


        }


    })( jQuery, window.Epoch || ( window.Epoch = {} ) );

} );

jQuery( function () {
    Epoch.init();

} );

