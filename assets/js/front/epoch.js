/** globals epoch_vars */
jQuery( document ).ready( function ( $ ) {

    (function ( $, app ) {
        /**
         * Bootstrap
         *
         * @since 0.0.1
         */
        app.init = function() {
            //element for the form wrap
            app.form_wrap_el = document.getElementById( epoch_vars.form_wrap );

            //element for comments area
            app.comments_wrap_el = document.getElementById( epoch_vars.comments_wrap );

            //element for template
            app.template_el = document.getElementById( epoch_vars.comments_template_id );

            //stores comments IDs we already Have in DOM
            app.comments_store = [];

            //Will be set to true if post has no comments, may be use to shut down system in app.shutdown
            app.no_comments = false;

            //Will be set to true if comments are closed, may be use to shut down system in app.shutdown
            app.comments_close = false;

            //used to stop the system
            app.shut_it_off = false;

            /**
             * Start the system
             */
            app.comments_open();
            app.comment_count( false );

            /**
             * Poll for new comments when page is visible only.
             */
            Visibility.every( epoch_vars.epoch_options.interval, function () {
                console.log( app.shut_it_off );
                if ( false == app.shut_it_off ) {
                    console.log( 44);
                    app.comment_count( true );
                }
            });


        };

        /**
         * Check if comments are open for current post
         *
         * @since 0.0.1
         */
        app.comments_open = function() {
            $.post(
                epoch_vars.api_url, {
                    action: 'comments_open',
                    epochNonce: epoch_vars.nonce,
                    postID: epoch_vars.post_id
                } ).done( function( response  ) {

                } ).success( function( response ) {
                    response = app.get_data_from_response( response );
                    if ( true == response ) {
                        app.get_form()
                    }else{
                        app.comments_closed = true;
                    }

                }
            );

        };

        /**
         * Get comment form
         *
         * @since 0.0.1
         */
        app.get_form = function() {
            spinner = document.getElementById( 'comment_form_spinner_id' );
            $( spinner ).show();
            $.post(
                epoch_vars.api_url, {
                    action: 'form',
                    epochNonce: epoch_vars.nonce,
                    postID: epoch_vars.post_id
                }
                ).done( function( response  ) {
                    $( spinner ).hide();
                } ).success( function( response ) {
                    response = app.get_data_from_response( response );
                    if ( 'undefined' != response.html ) {
                        app.form_wrap_el.innerHTML = response.html;
                        app.form_el = document.getElementById( epoch_vars.form_id );
                        app.form_el.removeAttribute( 'action' );
                        app.form_el.setAttribute( 'action', 'post' );

                        /**
                         * Submit form data
                         *
                         * @since 0.0.1
                         */
                        $( app.form_el ).submit( function( event ) {
                            event.preventDefault();


                            data = $( this ).serializeArray();
                            $.post(
                                epoch_vars.submit_api_url,
                                data
                            ).complete( function () {

                            } ).success( function ( response ) {
                                    app.form_el.reset();

                                    response = app.get_data_from_response( response );
                                    id = response.comment_id;

                                    app.get_comment( id );


                            } ).fail( function ( xhr ) {

                            } );



                        });

                    }

                }

            );

        };

        /**
         * Get comment count
         *
         * @since 0.0.1
         *
         * @param bool updateCheck If true we are using this to check if comments have change, use false on initial load
         */
        app.comment_count = function( updateCheck ) {
                app.shut_it_off = true;
                $.post(
                    epoch_vars.api_url, {
                        action: 'comment_count',
                        epochNonce: epoch_vars.nonce,
                        postID: epoch_vars.post_id
                    } ).done( function ( response ) {
                        app.shut_it_off = false;
                    } ).success( function ( response ) {
                        response = app.get_data_from_response( response );
                        if ( 'undefined' != response.count && 0 < response.count ) {
                            if ( false == updateCheck ) {
                                app.get_comments();
                                app.last_count = response.count;
                            } else {
                                if ( response.count > app.last_count ) {
                                    app.get_comments();


                                    app.last_count = response.count;
                                }
                            }
                        } else {
                            app.no_comments = true;
                        }
                    }
                );

        };

        /**
         * Get comments
         *
         * @since 0.0.1
         */
        app.get_comments = function() {
            app.shut_it_off = true;
            spinner = document.getElementById( 'comments_area_spinner_id' );
            $( spinner ).show();
            $.post(
                epoch_vars.api_url, {
                    action: 'get_comments',
                    epochNonce: epoch_vars.nonce,
                    postID: epoch_vars.post_id,
                    i: app.comments_store
                }
                ).done( function( response  ) {
                    $( spinner ).hide();
                    app.shut_it_off = false;

                } ).success( function( response ) {
                    response = app.get_data_from_response( response );
                    if ( 'object' == typeof response && 'undefined' != response && 'undefined' != response.comments ) {
                        comments = response.comments;
                        comments = JSON.parse( comments );
                        depth = epoch_vars;


                        if ( 'undefined' !== comments && 0 < comments.length ) {
                            $.each( comments, function ( key, comment ) {
                                app.comments_store.push( comment.comment_ID );
                                app.parse_comment( comment, false, 0 );

                                //parse its children if it has them and threaded comments is on
                                if ( 1 != depth ) {
                                    parent_id = comment.comment_ID;
                                    app.parse_children( comment, parent_id, 1 );
                                }

                            } );
                        }

                    }

                }

            );

        };

        /**
         * Get a single comment
         *
         * @since 0.0.5
         */
        app.get_comment = function( id ) {
            app.shut_it_off = true;
            spinner = document.getElementById( 'comments_area_spinner_id' );
            $( spinner ).show();

            $.post(
                epoch_vars.api_url, {
                    action: 'get_comment',
                    epochNonce: epoch_vars.nonce,
                    commentID: id
                }
            ).done( function( response  ) {
                    $( spinner ).hide();
                    app.shut_it_off = false;

            } ).success( function( response ) {

                    response = app.get_data_from_response( response );

                    if ( 'object' == typeof response && 'undefined' != response && 'undefined' != response.comment ) {
                        comment = response.comment;


                        app.comments_store.push( comment.comment_ID );
                        app.parse_comment( comment, comment.parent, 0 );

                    }

            } );

        };

        /**
         * Parse children of comment
         *
         * @since 0.0.4
         *
         * @param comment
         * @param parent_id
         */
        app.parse_children = function( comment, parent_id, level ) {

            if ( 'undefined' != comment ) {

                if (  false != comment.children  ) {

                    children = comment.children;
                    size = children.length;
                    if ( 0 != size ) {
                        for ( c = 0; c < size; c++ ) {
                            comment = children[ c ];
                            pid = comment.comment_ID;
                            app.parse_comment( comment, parent_id, level );
                            if ( false != comment.children ) {
                                level++;
                                app.parse_children( comment, pid, level );
                            }

                        }

                    }

                }

            }

        };

        /**
         * Utility function to get data key of responses.
         *
         * @since 0.0.1
         *
         * @param response
         * @returns {*}
         */
        app.get_data_from_response = function( response ) {
            return response.data;
        };

        /**
         * Parses content and outputs to DOM with the handlebars template
         *
         * @since 0.0.1
         *
         * @param comment
         */
        app.parse_comment = function( comment, parent_id, level ) {

            source = $( app.template_el ).html();
            template = Handlebars.compile( source );
            html = template( comment );

            if ( false == parent_id ) {
                $( html ).appendTo( app.comments_wrap_el );
            }else {
                html = '<div class="epoch-child child-of-' + parent_id +' level-' + level + ' ">' + html + '</div>';
                $( html ).appendTo( app.comments_wrap_el );

            }

            $( '.comment-reply-link' ).click( function( event ) {
                event.preventDefault;
            });
        };



    })( jQuery, window.Epoch || ( window.Epoch = {} ) );

} );

jQuery( function () {
    Epoch.init();

} );

