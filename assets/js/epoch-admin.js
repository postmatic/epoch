/* globals jQuery, _, EpochAdmin */
jQuery( document ).ready( function ( $ ) {


    $( '#epoch-admin-settings' ).on( 'submit', function (e) {
        e.preventDefault();
        var $spinner = $( '#epoch-spinner');
        $spinner.attr( 'aria-hidden', false ).show().css( 'visibility', 'visible' );
        var infinity = document.getElementById('epoch-infinity_scroll').checked;
        
        var data = {
            order: $( '#epoch-order' ).val(),
            per_page: $( '#epoch-per_page' ).val(),
            before_text: $( '#epoch-before_text' ).val(),
            infinity_scroll: infinity,
            _wp_http_referer: $( '[name="_wp_http_referer"]' ).val(),
            _wpnonce: $( '#_wpnonce' ).val(),
            action: "epoch_settings"
        };
        $.post( ajaxurl, data ).done( function () {
           alert( EpochAdmin.saved );
        }).error( function () {
            alert( EpochAdmin.error );
        }).always( function () {
            $spinner.attr( 'aria-hidden', true ).hide().css( 'visibility', 'hidden' );
        })

    });
} );
