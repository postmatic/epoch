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
            //element for the form wrap
            app.form_wrap_el = document.getElementById( epoch_vars.form_wrap );

            //element for comments area
            app.comments_wrap_el = document.getElementById( epoch_vars.comments_wrap );

            //element for template
            app.template_el = document.getElementById( epoch_vars.comments_template_id );

            //element for comment count
            app.count_el = document.getElementById( epoch_vars.count_id );

            //stores number of comments we have in the DOM.
            app.last_count = 0;

            //highest comment ID we have parsed
            app.highest_id = 0;

            //Will be set to true if post has no comments, may be use to shut down system in app.shutdown
            app.no_comments = false;

            //Will be set to true if comments are closed, may be use to shut down system in app.shutdown
            app.comments_close = false;

            //used to stop the system
            app.shut_it_off = false;

            /**
             * change action for comment form
             */
            if ( null != app.form_el) {
                app.form_el.removeAttribute( 'action' );
                app.form_el.setAttribute( 'action', 'post' );
            }


            /**
             * OK, now really go.
             */
            app.comments_open();
            app.comment_count( false );
            app.add_form();

            /**
             * Poll for new comments when page is visible only.
             */
            Visibility.every( epoch_vars.epoch_options.interval, function () {
                if ( false == app.shut_it_off ) {
                    app.comment_count( true );
                }
            });



        } //init

        /**
         * Check if comments are open for current post
         *
         * @since 0.0.1
         */
        app.comments_open = function() {
            app.shut_it_off = true;
            $.post(
                epoch_vars.api_url, {
                    action: 'comments_open',
                    epochNonce: epoch_vars.nonce,
                    postID: epoch_vars.post_id
                } ).fail( function( response  ) {
                    app.shut_it_off = false;
                } ).success( function( response ) {
                    response = app.get_data_from_response( response );
                    if ( true == response ) {
                        app.shut_it_off = false;
                    }else{
                        app.comments_closed = true;
                    }



                }
            );

        };


        /**
         * Get comment count
         *
         * @since 0.0.1
         *
         */
        app.comment_count = function( updateCheck ) {
            app.shut_it_off = true;
            $.post(
                epoch_vars.api_url, {
                    action: 'comment_count',
                    epochNonce: epoch_vars.nonce,
                    postID: epoch_vars.post_id
                } ).fail( function ( response ) {
                    app.shut_it_off = false;
                } ).success( function ( response ) {
                    response = app.get_data_from_response( response );

                    if ( 'undefined' != response.count && 0 < response.count ) {
                        if ( updateCheck ) {
                            if ( response.count > app.last_count ) {
                                app.new_comments();

                            }
                        }else{
                            app.get_comments();
                        }


                        app.set_last_count( response.count );

                    } else {
                        app.no_comments = true;
                    }

                    app.shut_it_off = false;
                }
            );

        };

        /**
         * Get comments, use of inital load.
         *
         * @since 0.0.1
         */
        app.get_comments = function() {
            app.shut_it_off = true;

            $.post(
                epoch_vars.api_url, {
                    action: 'get_comments',
                    epochNonce: epoch_vars.nonce,
                    postID: epoch_vars.post_id,
                    highest: 0
                } ).done( function( response  ) {
                    app.shut_it_off = false;

                } ).success( function( response ) {
                    response = app.get_data_from_response( response );

                    app.comment_response( response, false );

                }

            );

        };

        /**
         * Takes response from get_comments & new_comment and parses them properly
         *
         * @since 0.0.11
         *
         * @param response
         */
        app.comment_response = function ( response, is_new ) {

            if ( 'object' == typeof response && 'undefined' != response && 'undefined' != response.comments ) {
                comments = response.comments;
                comments = JSON.parse( comments );
                depth = epoch_vars;



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

                    var comment;

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
                    response = app.get_data_from_response( response );

                    app.comment_response( response, true );


                }

            );

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
         * @param comment Comment object
         */
        app.parse_comment = function( comment ) {
            parent_id = comment.comment_parent;
            source = $( app.template_el ).html();
            template = Handlebars.compile( source );
            html = template( comment );
            return html;
        };

        /**
         * Put a parsed comment into the DOM
         *
         * @param html The actual HTML.
         * @param parent_id ID of parent, or 0 for top level comment.
         * @param level The threading level, not needed for top-level comments.
         */
        app.put_comment_in_dom = function( html, parent_id, level, id ) {

            if ( 0 == comment.comment_parent && 'DESC' == epoch_vars.epoch_options.order ) {
                first_child = app.comment_inner_wrap_el.firstChild;
                new_el = document.createElement( 'div' );
                new_el.innerHTML = html;
                app.comment_inner_wrap_el.insertBefore( new_el, first_child );
            } else {

                if ( 0 == parent_id ) {
                    $( html ).appendTo( app.comment_inner_wrap_el );
                } else {
                    html = '<div class="epoch-child child-of-' + parent_id + ' level-' + level + ' ">' + html + '</div>'

                    parent_el = document.getElementById( 'comment-' + parent_id );
                    if ( null != parent_el ) {
                        $( html ).appendTo( parent_el );
                    } else {

                        $( html ).appendTo( app.comment_inner_wrap_el );
                    }

                }
            }

            $( '.comment-reply-link' ).click( function( event ) {
                event.preventDefault();
            });
        };

        app.add_form = function() {
            $.post(
                epoch_vars.submit_api_url,
                {
                    action: 'get_comment_form',
                    epochNonce: epoch_vars.nonce,
                    postID: epoch_vars.post_id
                }
            ).success( function ( response ) {
                app.commenting_area = document.createElement( 'div' );
                app.commenting_area.id = app.form_wrap;
                app.commenting_area.innerHTML = response.html;
                $( app.comment_iframe_el ).contents().find( 'body' ).append( app.commenting_area );

                /**
                 * Submit form data
                 *
                 * @since 0.0.1
                 */
                $( app.form_el ).submit( function ( event ) {
                    event.preventDefault();
                    app.shut_it_off = true;

                    //validate fields
                    fail = false;
                    fail_log = '';
                    $( app.form_el ).find( 'select, textarea, input' ).each( function () {
                        if ( !$( this ).prop( 'required' ) ) {

                        } else {
                            if ( !$( this ).val() ) {
                                fail = true;
                                name = epoch_ucwords( $( this ).attr( 'name' ) );
                                fail_log += name + ' ' + epoch_translation.is_required + ".\n";
                            }

                        }
                    } );

                    //submit if fail never got set to true
                    if ( !fail ) {

                        $( app.form_el ).find( 'input[type="submit"]' ).attr( 'disabled', 'disabled' );

                        data = $( this ).serializeArray();
                        $.post(
                            epoch_vars.submit_api_url,
                            data
                        ).complete( function () {

                                $( app.form_el ).find( 'input[type="submit"]' ).removeAttr( 'disabled' );
                            } ).success( function ( response ) {

                                if ( !response.success ) {
                                    alert( epoch_translation.comment_rejected );
                                    return false;
                                }

                                $( 'textarea#comment' ).val( '' );
                                $( '#comment_parent' ).val( '0' );


                                //test if WordPress moved the form
                                temp_el = document.getElementById( 'wp-temp-form-div' );
                                if ( null != temp_el ) {
                                    respond_el = document.getElementById( 'respond' );
                                    $( respond_el ).insertAfter( temp_el );

                                }

                                app.set_last_count( app.last_count + 1 );
                                response = app.get_data_from_response( response );
                                var comment;
                                comment = response.comment;

                                id = parseInt( comment.comment_ID, 10 );
                                if ( app.highest_id < id ) {
                                    app.highest_id = id;
                                }

                                //parse if comment isn't in DOM already
                                if ( null == document.getElementById( 'comment-' + comment.comment_ID ) ) {
                                    html = app.parse_comment( comment );
                                    app.put_comment_in_dom( html, comment.comment_parent, comment.depth, id );
                                    comment_el = document.getElementById( 'comment-' + comment.comment_ID );
                                    if ( null != comment_el ) {
                                        $( comment_el ).addClass( 'epoch-success' ).delay( 100 ).queue( function ( next ) {
                                            $( this ).removeClass( 'epoch-success' );
                                            next();
                                        } );

                                    }
                                }

                                app.shut_it_off = false;

                            } ).fail( function ( xhr ) {
                                if ( xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message ) {
                                    alert( $( '<div/>' ).html( xhr.responseJSON.data.message ).text() );
                                }
                                $( app.form_wrap_el, 'textarea#comment' ).addClass( 'epoch-failure' ).delay( 100 ).queue( function ( next ) {
                                    $( this ).removeClass( 'epoch-failure' );
                                    next();
                                } );
                            } );
                    } else {
                        $( app.form_wrap_el, 'textarea#comment' ).addClass( 'epoch-failure' ).delay( 100 ).queue( function ( next ) {
                            $( this ).removeClass( 'epoch-failure' );
                            next();
                        } );
                        alert( fail_log );
                    }
                } );
            });

        };


        /**
         * Store last count
         *
         * @since unknown
         *
         * @param count
         */
        app.set_last_count = function( count ) {
            app.last_count = count;
            $( app.count_el ).text( count );
        };

    })( jQuery, window.Epoch_Inside || ( window.Epoch_Inside = {} ) );
    jQuery( function () {
        Epoch_Inside.init();

    } );

} );

