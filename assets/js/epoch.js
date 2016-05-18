/* globals jQuery, EpochFront */
jQuery( document ).ready( function ( $ ) {
    var epoch = new Epoch( $, EpochFront );
    epoch.init();
} );


function Epoch( $, EpochFront  ) {
    var self = this;
    var page = 1;
    var post;
    var listEl;

    this.init = function (  ) {
        listEl = document.getElementById( 'epoch-comment-list' );
        post = EpochFront.post;
        this.getComments( EpochFront.first_url );


    };
    
    this.getComments = function ( url ) {
        var comments = this.api( url );
        listEl.innerHTML = comments.template;
    };

    this.api = function( url ) {
        var key = 'epoch-cache' + url;

        var local = localStorage.getItem( key );

        if ( ! _.isString( local ) || "null" == local ) {
            return $.get( url ).then( function ( r ) {
                localStorage.setItem( key, JSON.stringify( r ) );
                return r;
            } );

        }else {
            return JSON.parse( local );

        }

    };
    
    
    
    
}
