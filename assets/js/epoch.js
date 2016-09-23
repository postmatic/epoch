/* globals jQuery, EpochFront */
jQuery( document ).ready( function ( $ ) {
    if( null != document.getElementById( 'epoch-comments' ) ) {
        var epoch = new Epoch( $, EpochFront );
        epoch.init();
    }

    $.fn.EpochserializeObject = function() {
        var arrayData, objectData;
        arrayData = this.serializeArray();
        objectData = {};

        $.each(arrayData, function() {
            var value;

            if (this.value != null) {
                value = this.value;
            } else {
                value = '';
            }

            if (objectData[this.name] != null) {
                if (!objectData[this.name].push) {
                    objectData[this.name] = [objectData[this.name]];
                }

                objectData[this.name].push(value);
            } else {
                objectData[this.name] = value;
            }
        });

        return objectData;
    };

} );


function Epoch( $, EpochFront ) {
    var self = this;
    var post;
    var page = 1;
    var areaEl;
    var nextURL;
    var prevURL;
    var lastURL;
    var total;

    /**
     * Make Epoch go
     *
     * @since 2.0.0
     */
    this.init = function () {
        areaEl = document.getElementById( 'epoch-comments' );
        post = EpochFront.post;

        if ( window.location.hash && window.location.hash.startsWith( '#comment-' ) ) {
            var hash = window.location.hash;
            var id = hash.replace( '#comment-', '' );
            self.show( $( '#epoch-load-all' ) );
            $.when( self.getThread( id ) ).then( function () {
                if ( EpochFront.infinity ) {
                    self.infinityScroll();
                }
            } );
        } else {
            $.when( self.getComments( EpochFront.first_url ) ).then( function () {
                if ( EpochFront.infinity ) {
                    self.infinityScroll();
                }
            } );
            self.setupNav();
            self.setupForm();
        }

        var $body = $( 'body' );

        /**
         * Add our event handlers for post comment focusing/scrolling after new comment
         *
         * @since 2.0.0
         */
        function mainScrollFocus( $body ) {

            $body.on( 'epoch.two.comment.posted', function ( e ) {

                var commentID = e.comment_id;
                $body.on( 'epoch.two.comments.loaded', function ( e ) {
                    self.addFocus( commentID );
                    self.scrollTo( 'comment-' + commentID );
                } );

            } );
        };

        /**
         * Add our event handlers for post comment focusing/scrolling after laoding a thread
         *
         * @since 2.0.0
         */
        function threadScrollFocus(  $body ) {
            $body.on( 'epoch.two.commentsThread.loaded', function (  e ) {
                var commentID = e.focus;
                self.addFocus( commentID );
                self.scrollTo( 'comment-' + commentID );
            });
        };


        mainScrollFocus( $body );
        threadScrollFocus( $body );

    };

    /**
     * Get a page of comments
     *
     * @since 2.0.0
     *
     * @param url URL for request
     */
    this.getComments = function ( url ) {
        lastURL = url;
        return $.when( this.api( url ) ).then( function ( r ) {
            nextURL = r.next;
            prevURL = r.prev;
            areaEl.innerHTML = r.template;
            self.hideShowNav();

        } );

    };

    /**
     * Get a thread of comments
     *
     * @since 2.0.0
     *
     * @param id One comment ID in the thread
     */
    this.getThread = function ( id ) {
        var url = EpochFront.api + '/comments/threaded/' + id + '?nonce=' + EpochFront.nonce + '&_wpnonce=' + EpochFront._wpnonce;
        $.when( this.api( url ) ).then( function ( r ) {
            areaEl.innerHTML = r.template;
            $( 'body' ).triggerHandler({
                type:"epoch.two.commentsThread.loaded",
                focus: id
            });

            self.hide( $( '#epoch-navigation' ) );
            self.show( $( '#epoch-load-all' ) );
            $( '#epoch-load-all a' ).on( 'click', function ( e ) {
                e.preventDefault();
                window.location.hash = '#epoch-commenting';
                self.getComments( EpochFront.first_url );
            } );


        } );
    };

    /**
     * Get a page of comments
     *
     * @since 2.0.0
     *
     * @param url URL for request
     */
    this.api = function ( url ) {
        var key = 'epoch-cache' + url;
        var pages;
        var local = localStorage.getItem( key );
        url += '&epoch=true';

        if ( !_.isString( local ) || "null" == local ) {
            return $.get( url ).then( function ( r, textStatus, rObj ) {
                localStorage.setItem( key, JSON.stringify( r ) );
                r.prev = rObj.getResponseHeader( 'X-WP-EPOCH-PREVIOUS' );
                r.next = rObj.getResponseHeader( 'X-WP-EPOCH-NEXT' );
                pages = rObj.getResponseHeader( 'X-WP-EPOCH-TOTAL-PAGES' );
                total = rObj.getResponseHeader( 'X-WP-EPOCH-TOTAL-COMMENTS' );
                total = rObj.getResponseHeader( 'X-WP-EPOCH-TOTAL-COMMENTS' );
                $( '#epoch-count' ).html( total );
                $( 'body' ).triggerHandler({
                    type:"epoch.two.comments.loaded",
                    page: page,
                    post: post
                });

                return r;
            } );

        } else {
            $( 'body' ).triggerHandler({
                type:"epoch.two.comments.loaded",
                page: page,
                post: post
            });
            return JSON.parse( local );

        }

    };

    /**
     * Setup navigation
     *
     * @since 2.0.0
     */
    this.setupNav = function () {
        $( '#epoch-prev' ).on( 'click', function ( e ) {
            e.preventDefault();
            page--;
            $.when( self.getComments( prevURL ) ).then( function () {
                self.scrollTo( 'epoch-wrap' );
            } );
        } );
        $( '#epoch-next' ).on( 'click', function ( e ) {
            e.preventDefault();
            page++;
            $.when( self.getComments( nextURL ) ).then( function () {
                self.scrollTo( 'epoch-wrap' );
            } );

        } );
    };

    /**
     * Hide element
     *
     * @since 2.0.0
     */
    this.hide = function ( $el ) {
        $el.hide().addClass( 'epoch-hide' ).attr( 'aria-hidden', 'true' );
    };

    /**
     * Show element
     *
     * @since 2.0.0
     */
    this.show = function ( $el ) {
        $el.show().removeClass( 'epoch-hide' ).attr( 'aria-hidden', 'false' );
    };

    /**
     * Set hide and show status for comment nav
     *
     * @since 2.0.0
     */
    this.hideShowNav = function () {
        self.show( $( '#epoch-navigation' ) );
        if ( 0 == nextURL ) {
            self.hide( $( '#epoch-next' ) );
        } else {
            self.show( $( '#epoch-next' ) );
        }

        if ( 0 == prevURL ) {
            self.hide( $( '#epoch-prev' ) );
        } else {
            self.show( $( '#epoch-prev' ) );
        }

        self.hide( $( '#epoch-load-all' ) );
    };

    /**
     * Setup comment form
     *
     * @since 2.0.0
     */
    this.setupForm = function () {
        var $form = $( '#commentform' );
        $form.removeAttr( 'action' );
        $form.on( 'submit', function ( e ) {
            e.preventDefault();
            var fail = false;
            var fails = [];




            $form.find( 'select, textarea, input' ).each( function () {
                if ( !$( this ).prop( 'required' ) ) {

                } else {
                    if ( !$( this ).val() ) {
                        fail = true;
                        fails.push( $( this ).attr( 'id' ) );
                    }

                }
            } );

            if ( fail ) {
                $( '.epoch-failure' ).removeClass( 'epoch-failure' ).removeAttr( 'aria-invalid' );
                if ( 0 < fails.length ) {
                    $.each( fails, function ( i, the_fail ) {
                        the_fail = document.getElementById( the_fail );
                        if ( null !== the_fail ) {
                            $( the_fail ).parent().addClass( 'epoch-failure' ).attr( 'aria-invalid', true );
                        }
                    } );
                }
            } else {
                var data = $form.EpochserializeObject();


                data.author_name = data.author;
                data.author_url  = data.url;
                data.post = data.comment_post_ID;
                data.parent = data.comment_parent;
                data.content = data.comment;
                if ( 0 != EpochFront.user_email ) {
                    data.author_email = EpochFront.user_email;
                }else{
                    data.author_email = data.email;
                }

                data.author_email = encodeURI( data.author_email );
                delete data.author;
                data.epoch = true;
                data._wpnonce = EpochFront._wpnonce;
                $.post( EpochFront.comments_core, data ).done( function ( r, textStatus, rObj ) {

                    total = rObj.getResponseHeader( 'X-WP-EPOCH-TOTAL-COMMENTS' );
                    $( '#epoch-count' ).html( total );
                    $form[ 0 ].reset();
                    var url;
                    if ( EpochFront.infinity ) {
                        url = EpochFront.first_url + '&all=true';
                    }else{
                        url = lastURL;
                    }

                    $( 'body' ).triggerHandler({
                        type:"epoch.two.comment.posted",
                        page: page,
                        post: post,
                        comment_id: r.id,
                        comment: r
                    });

                    $.when( self.getComments( url ) ).done( function () {
                        self.addFocus( r.id );
                        self.scrollTo( 'comment-' + r.id );
                    } );

                } ).error( function ( error ) {
                    var message = EpochFront.comment_rejected;
                    if ( _.isObject( error ) && _.has( error, 'responseJSON' ) ) {
                        error = error.responseJSON;
                        if ( _.has( error, 'code' ) && 'rest_invalid_param' == error.code && _.has( error.data, 'params' ) ) {
                            message = '';
                            $.each( error.data.params, function ( param, m ) {
                                message += m;
                            } )
                        } else if ( _.isObject( error ) && _.has( error, 'data' ) && _.has( error.data, 'message' ) ) {
                            message = error.data.message;
                        } else if ( _.isObject( error ) && _.has( error, 'message' ) ) {
                            message = error.message;
                        }
                    }

                    alert( message );

                } );
            }

        } );
    };

    /**
     * Scroll to an element
     *
     * @since 2.0.0
     *
     * @param id ID of element (not comment)
     */
    this.scrollTo = function ( id ) {
        var el = document.getElementById( id );
        if ( null == el ) {
            el = document.getElementById( 'epoch-wrap' );
        }

        if ( null != el ) {
            var position = $( el ).offset();
            window.scrollTo( position.left, position.top );

        }
    };

    /**
     * Add the epoch-focus class to a comment element
     *
     * @since 2.0.0
     *
     * @param id The comment's ID. '#div-comment-' is prefixed to it.
     */
    this.addFocus = function ( id ) {
        $( '.epoch-focus' ).removeClass( 'epoch-focus' );
        $( '#div-comment-' + id ).addClass( 'epoch-focus' );
    };

    /**
     * Do infinity scrolling if called for
     *
     * @since 2.0.0
     */
    this.infinityScroll = function () {
        var loading = false;
        var allLoaded = false;
        var $el = $( '#epoch-comments' );
        var startHeight = $el.offset().top + $el.outerHeight( true ) - 600;

        self.hideShowNav = function(){};
        $( '#epoch-navigation' ).remove();

        var $spinner = $( '#epoch-infinity-spinner' );
        $( document ).on( 'scroll', function () {

            if ( _.isUndefined( nextURL ) || true === loading || true  === allLoaded || page >= total ){
                return;
            }

            var position = document.documentElement.scrollTop || document.body.scrollTop;
            if ( position > startHeight ) {
                self.show( $spinner );
                loading = true;
                var url = nextURL;
                if ( 0 != lastURL ) {
                    lastURL = url;
                }
                page++;
                if ( page == total ){
                    allLoaded = true;
                }
                $.when( self.api( url ) ).then( function ( r ) {
                    nextURL = r.next;
                    if (  0 == nextURL ){
                        allLoaded = true;
                    }
                    prevURL = r.prev;
                    $( areaEl ).append( r.template );
                    loading = false;
                    self.hide( $spinner );
                } );

            }
        } );
    };

}



