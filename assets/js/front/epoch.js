/** globals epoch_vars */
jQuery( document ).ready( function ( $ ) {

    (function ( $, app ) {
        //element for comments area
        app.comments_wrap_el = document.getElementById( epoch_vars.wrap_id );

        //element for template
        app.template_el = document.getElementById( epoch_vars.comments_template_id );

        //highest ID of comment we've parsed so far
        app.highest_id = 0;

        //count # of comments we have
        app.last_count = 0;

        app.shut_it_off = false;

        /**
         * Bootstrap
         *
         * @since 0.0.1
         */
        app.init = function() {
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
            app.comment_iframe_el.id = epoch_vars.iframe_id;


            //append iFrame to DOM
            $( app.comment_iframe_el ).appendTo( app.comments_wrap_el );

            $( 'iframe#epoch-comment-iframe').ready(function() {


                //add JS inside
                app.add_script( epoch_vars.iframe_js );


                app.add_script( epoch_vars.iframe_visibility );
                app.add_script( epoch_vars.iframe_handlebars );
                app.add_script( epoch_vars.iframe_handlebars_helpers );

                //add CSS inside
                var style = document.createElement( 'link' );
                style.setAttribute( 'href', epoch_vars.iframe_css );
                style.setAttribute( 'rel', 'stylesheet' );
                style.setAttribute( 'type', 'text/css' );
                $( app.comment_iframe_el ).contents().find('head').append( style );

                $( app.comment_iframe_el ).contents().find('body' ).append( '<div id="' + epoch_vars.comments_wrap + '">hats</div><div id="'+  epoch_vars.form_wrap + '"></div>' );

                app.epoch();
            });






        };






        /**
         * Run the Epoch comment system inside of the iFrame
         */
        app.epoch = function() {

            app.request( 'comment_count', function ( response ) {
                app.last_count = response.data.count;
            }, app.nothing );
            app.request( 'get_comment_form', app.setup_form, app.nothing );
            app.request( 'get_comments', app.get_comments, function() {
                    $(app.inner ).html( 'No comments found' );
            });
            /**
             * Poll for new comments when page is visible only.
             */
            Visibility.every( epoch_vars.epoch_options.interval, function () {

                if ( false == app.shut_it_off ) {
                    app.request( 'comment_count', app.check_count, function () {
                        app.shut_it_down = true;
                    });
                }
            });


        };

        /**
         * Check comment count and get new comments if needed
         *
         * @since 0.4.0
         *
         * @param response
         */
        app.check_count = function( response ) {
            var count = response.data.count;
            if ( count > app.last_count ) {
                app.new_comments();
            }
        };

        /**
         * Setup the form inside iFrame
         *
         * @since 0.4.0
         *
         * @param response
         */
        app.setup_form = function( response ) {
            console.log( response.data );
            console.log( $( app.comment_iframe_el ).contents().find( epoch_vars.form_wrap ) );
           // $( app.comment_iframe_el ).contents().find( epoch_vars.form_wrap ).append( response.data );
            //$( 'iframe#epoch-comment-iframe' ).append(response.data );
            var  el = document.createElement( 'div' );
            el.innerHTML = response.data;
            $( app.comment_iframe_el ).contents().find( epoch_vars.form_wrap ).append( el );
            $( 'iframe#epoch-comment-iframe' ).append( el );
            console.log( $( app.comment_iframe_el ).contents().find( epoch_vars.form_wrap ) );


        };

        /**
         * Pase through to comment response of succes of get_comments action
         *
         * @since 0.4.0
         *
         * @param response
         */
        app.get_comments = function( response ) {

            app.comment_response( response.data, false );


        };

        /**
         * Takes response from get_comments & new_comment and parses them properly
         *
         * @since 0.0.11
         *
         * @param response
         */
        app.comment_response = function ( response, is_new ) {
            var comments;
            if ( 'object' == typeof response && 'undefined' != response && 'undefined' != response.comments ) {
                comments = response.comments;
                comments = JSON.parse( comments );
                depth = epoch_vars.depth;

                if ( 'undefined' !== comments && 0 < comments.length ) {
                    var parents = [];
                    var children = [];
                    $.each( comments, function ( key, comment ) {
                        id = parseInt( comment.comment_ID, 10 );
                        if ( app.highest_id < id ) {
                            app.highest_id = id;
                        }

                        if ( 0 == comment.comment_parent ) {
                            parents.push( comment );
                        }else{
                            children.push( comment );
                        }

                    });

                    if ( 'ASC' == epoch_vars.epoch_options.order ) {
                        children.reverse();
                        parents.reverse();
                    }else{
                        children.reverse();
                    }

                    $.each( parents, function( key, comment ) {
                        html = app.parse_comment( comment );

                        app.put_comment_in_dom( html, comment.comment_parent, comment.depth, parseInt( comment.comment_ID, 10 ) );

                        if ( is_new ) {

                            comment_el = document.getElementById( 'comment-' + comment.comment_ID );
                            if ( null != comment_el ) {
                                $( comment_el ).addClass( 'epoch-success' ).delay( 100 ).queue( function ( next ) {
                                    $( this ).removeClass( 'epoch-success' );
                                    next();
                                } );

                            }
                        }

                    });

                    $.each( children, function( key, comment )  {
                        html = app.parse_comment( comment );
                        app.put_comment_in_dom( html, comment.comment_parent, comment.depth, parseInt( comment.comment_ID, 10 ) );
                    });


                }

            }


        };

        /**
         * Parses content and outputs to DOM with the handlebars template
         *
         * @since 0.0.1
         *
         * @param comment Comment object
         */
        app.parse_comment = function( comment ) {
            parent_id = comment.comment_parent;
            source = $( epoch_vars.iframe_comment_template ).html();
            template = Handlebars.compile( source );
            html = template( comment );
            return html;
        };

        /**
         * Put a parse comment into the DOM
         *
         * @param html The actual HTML.
         * @param parent_id ID of parent, or 0 for top level comment.
         * @param level The threading level, not needed for top-level comments.
         */
        app.put_comment_in_dom = function( html, parent_id, level, id ) {

            if ( 0 == parent_id && 'DESC' == epoch_vars.epoch_options.order ) {
                first_child = app.comments_wrap_el.firstChild;
                new_el = document.createElement( 'div' );
                new_el.innerHTML = html;
                app.comments_wrap_el.insertBefore( new_el, first_child );
            } else {

                if ( 0 == parent_id ) {
                    $( html ).appendTo( app.comments_wrap_el );
                } else {
                    html = '<div class="epoch-child child-of-' + parent_id + ' level-' + level + ' ">' + html + '</div>';

                    parent_el = document.getElementById( 'comment-' + parent_id );
                    if ( null != parent_el ) {
                        $( html ).appendTo( parent_el );
                    } else {

                        $( html ).appendTo( app.comments_wrap_el );
                    }

                }
            }

            $( '.comment-reply-link' ).click( function( event ) {
                event.preventDefault();
            });
        };

        /**
         * Add script to the iFrame header
         *
         * @param src Source for script
         */
        app.add_script = function( src ) {
            var script = document.createElement( 'script' ) ;
            script.setAttribute('src', src );
            script.setAttribute('type', 'text/javascript' );
            $( app.comment_iframe_el ).contents().find('head').append( script );
        }

        /**
         * Make a request to our API
         *
         * @since 0.4.0
         *
         * @param action
         * @param success
         * @param failure
         */
        app.request = function( action, success, failure ) {
            $.ajax( {
                method: "POST",
                beforeSend: app.before,
                fail: failure,
                complete: app.done,
                url: epoch_vars.api_url,
                data: {
                    action: action,
                    epochNonce: epoch_vars.nonce,
                    postID: epoch_vars.post_id
                },
                success: success
            });


        };

        /**
         * Turn off the system before making a request.
         *
         * @since 0.4.0
         */
        app.before = function() {

            app.shut_it_off = true;
        };

        /**
         * Turn it back on after making a request
         *
         * @since 0.4.0
         */
        app.done = function() {
            app.shut_it_off = false;
        };

        /**
         * This function doesn't do anything.
         *
         * @since 0.4.0
         */
        app.nothing = function() {

        };



        /**
         * Get comments, use for getting new comments
         *
         * @since 0.0.11
         */
        app.new_comments = function() {
            app.shut_it_off = true;

            $.post(
                epoch_vars.api_url, {
                    action: 'new_comments',
                    epochNonce: epoch_vars.nonce,
                    postID: epoch_vars.post_id,
                    highest: app.highest_id
                } ).done( function( response  ) {
                    app.shut_it_off = false;
                } ).success( function( response ) {
                    app.comment_response( response.data, true );


                }

            );

        };





    })( jQuery, window.Epoch || ( window.Epoch = {} ) );

} );

jQuery( function () {
    Epoch.init();

} );

