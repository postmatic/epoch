jQuery( document ).ready( function ( $ ) {
    (function ( $, app ) {
        /**
         * Bootstrap
         *
         * @since 0.0.1
         */
        app.init = function() {

            /**
             * Setup some vars
             */


            /**
             * change action for comment form
             */


            /**
             * OK, now really go.
             */

            /**
             * Poll for new comments when page is visible only.
             */
            Visibility.every( epoch_vars.epoch_options.interval, function () {
                if ( false == app.shut_it_off ) {
                    app.comment_count( true );
                }
            });



        } //init



    })( jQuery, window.Epoch_Inside || ( window.Epoch_Inside = {} ) );
    jQuery( function () {
        Epoch_Inside.init();

    } );

} );

