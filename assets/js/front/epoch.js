/** globals epoch_vars */
jQuery( document ).ready( function ( $ ) {

    (function ( $, app ) {
        /**
         * Bootstrap
         *
         * @since 0.0.1
         */
        app.init = function() {

            app.find_elements();

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

            //set max comment depth
            app.max_depth = parseInt( epoch_vars.depth );

            //holds ID of comment being replied to.
            app.parent_ID = 0;

            if ( null != app.form_el) {
                app.form_el.removeAttribute( 'action' );
                app.form_el.setAttribute( 'action', 'post' );
            }

            /**
             * Start the system
             */
            app.set_width();
            app.comments_open();
            app.initial_load = true;
            app.comment_count( false );
            window.onresize = function(event) {
                app.set_width();
            };

            /**
             * Poll for new comments when page is visible only.
             */
            if ( epoch_vars.live_mode ) {
                Visibility.every( epoch_vars.epoch_options.interval, function () {
                    if ( false == app.shut_it_off ) {
                        app.comment_count( true );
                    }
                });
            }

            /**
             * Set app.parent_ID on reply link click
             *
             * @since 1.0.11
             */
            $( document ).on( 'click', '.comment-reply-link', function(){
                app.parent_ID = $( this ).parent().data( 'comment-id' );
            } );

            /**
             * Submit form data
             *
             * @since 0.0.1
             */
            $( document ).on( 'submit', '#' + epoch_vars.form_id, function( event ) {
                event.preventDefault();
                app.shut_it_off = true;
                app.find_elements();
                app.initial_load = false;
                var comment = {};
                var pending_id;

                /**
                 * Put new comment in the DOM
                 *
                 * @since 1.0.11
                 *
                 * @param comment
                 */
                 function parse_new_comment( comment, pending ) {
                    if( true != pending ) {
                        var pending_el = document.getElementById( 'comment-' + pending );
                        if ( null != pending_el ) {

                            if ( 'ASC' == epoch_vars.epoch_options.order ) {
                                $( pending_el ).remove();
                            }else{
                                $( pending_el ).parent().remove();
                            }

                        }

                    }



                    //parse if comment isn't in DOM already
                    if ( null == document.getElementById( 'comment-' + comment.comment_ID ) ) {
                        html = app.parse_comment( comment );
                        var comment_el = document.getElementById( 'comment-' + comment.comment_ID );

                        app.put_comment_in_dom( html, comment.comment_parent, comment.depth, comment.comment_ID );


                        var comment_el = document.getElementById( 'comment-' + comment.comment_ID );
                        if ( null != comment_el ) {
                            $( comment_el ).addClass( 'epoch-success' ).delay( 100 ).queue( function ( next ) {
                                $( this ).removeClass( 'epoch-success' );
                                next();
                            } );

                        }

                    }


                    if( true == pending ) {
                        $( comment_el ).addClass( 'epoch-pending' );
                        $( comment_el ).find( '.epoch-comment-awaiting-moderation' ).remove();
                        $( comment_el ).find( '.epoch-comment-link' ).remove();
                    }

                    jQuery( 'body' ).triggerHandler( 'epoch.comment.posted', [ comment.comment_post_ID, comment.comment_ID ] );

                    /* Hide Moderation Class if Parent Approved */
                    if ( comment.parent_approved != '0' ) {
                        $comment_parent = jQuery( '#div-comment-' + comment.parent_approved );
                        $comment_parent.find( '.epoch-approve' ).remove();
                        $comment_parent.removeClass( 'epoch-wrap-comment-awaiting-moderation' );
                    }

                    app.parent_ID = 0;

                };

                //validate fields
                var fail = false;
                var fails = [];

                $( app.form_el ).find( 'select, textarea, input' ).each(function(){
                    if( ! $( this ).prop( 'required' )){

                    } else {
                        if ( ! $( this ).val() ) {
                            fail = true;
                            fails.push( $( this ).attr( 'id' ) );
                        }

                    }
                });

                //submit if fail never got set to true
                if ( ! fail ) {
                    $( '.epoch-failure' ).removeClass( 'epoch-failure' );

                    $( app.form_el ).addClass( 'epoch-submitting' )
                        .find( 'input[type="submit"]' )
                        .attr( 'disabled', 'disabled' );

                    var data = $( this ).serializeArray();

                    var pending_data = {};
                    $.each( data, function( i, obj ) {
                        pending_data[ obj.name ] = obj.value;
                    });

                    comment.comment_parent = app.parent_ID;
                    comment.comment_content = pending_data.comment;
                    var parts = comment.comment_content.split("\n");
                    comment.comment_content = parts.join("</p><p>");
                    comment.comment_content = "<p>" + comment.comment_content + "</p>";

                    if( '' != epoch_vars.user.comment_author ){
                        comment.comment_author = epoch_vars.user.comment_author;
                    } else if( pending_data.hasOwnProperty( 'author') ) {
                        comment.comment_author = pending_data.author;
                    }else{
                        comment.comment_author = '';
                    }

                    if( '' != epoch_vars.user.comment_author_url ){
                        comment.comment_author_url = epoch_vars.user.comment_author_url;
                    } else if( pending_data.hasOwnProperty( 'url') ) {
                        comment.comment_author_url = pending_data.url;
                    }else{
                        comment.comment_author_url = '';
                    }

                    if( '' != epoch_vars.user.author_avatar ){
                        comment.author_avatar = epoch_vars.user.author_avatar;
                    } else{
                        comment.author_avatar = epoch_vars.empty_avatar;
                    }

                    pending_id =  Math.floor(Math.random() * (4000 - 10 + 1)) + 10;
                    comment.comment_ID = pending_id;
                    parse_new_comment( comment, true );
                    $.post(
                        epoch_vars.submit_api_url,
                        data
                    ).complete( function () {

                            $( app.form_el ).removeClass( 'epoch-submitting' )
                                .find( 'input[type="submit"]' )
                                .removeAttr( 'disabled' );

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

                            app.set_last_count( parseInt(app.last_count) + 1 );
                            response = app.get_data_from_response( response );
                            var comment = response.comment;

                            id = parseInt( comment.comment_ID, 10 );
                            if ( app.highest_id < id ) {
                                app.highest_id = id;
                            }

                            parse_new_comment( comment, pending_id );
                            app.shut_it_off = false;

                        } ).fail( function ( xhr ) {
                            if ( xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message ) {
                                alert( $( '<div/>' ).html( xhr.responseJSON.data.message ).text() );
                            }
                            $( app.form_wrap_el, 'textarea#comment' ).addClass( 'epoch-failure' ).delay( 100 ).queue( function ( next ) {
                                $( this ).removeClass( 'epoch-failure' );
                                next();
                            } );

                            var pending_el = document.getElementById( 'comment-' + pending_id );
                            if ( null != pending_el ) {
                                if ( 'ASC' == epoch_vars.epoch_options.order ) {
                                    $( pending_el ).remove();
                                }else{
                                    $( pending_el ).parent().remove();
                                }
                            }
                        } );
                } else {
                    $( '.epoch-failure' ).removeClass( 'epoch-failure' );
                    if ( 0 < fails.length ) {
                        $.each( fails, function( i, the_fail ) {
                            the_fail = document.getElementById( the_fail );
                            if ( null !== the_fail ) {
                                $( the_fail ).parent().addClass( 'epoch-failure' );
                            }
                        });
                    }


                }
            });

        };

        /**
         * Set app element references.
         *
         * This should be redone any time the elements may have been removed or replaced.
         *
         * @since 1.0.9
         */
        app.find_elements = function() {

            //element for the form wrap
            app.form_wrap_el = document.getElementById( epoch_vars.form_wrap );

            //element for comments area
            app.comments_wrap_el = document.getElementById( epoch_vars.comments_wrap );

            //element for template
            app.template_el = document.getElementById( epoch_vars.comments_template_id );

            //element for comment count
            app.count_el = document.getElementById( epoch_vars.count_id );

            //change action for comment form
            app.form_el = document.getElementById( epoch_vars.form_id );
        };

        /**
         * Check if comments are open for current post
         *
         * @since 0.0.1
         */
        app.comments_open = function() {
            app.shut_it_off = true;
            $.when (
                $.post(
                    epoch_vars.api_url, {
                        action: 'comments_open',
                        epochNonce: epoch_vars.nonce,
                        postID: epoch_vars.post_id
                    } ).fail( function ( response ) {
                        app.shut_it_off = false;
                    } ).success( function ( response ) {
                        response = app.get_data_from_response( response );
                        if ( true == response ) {
                            app.shut_it_off = false;
                        } else {
                            app.comments_closed = true;
                        }


                    }
                )
            ).then( function(){
                    var loading = document.getElementById( epoch_vars.loading );
                    $( loading ).fadeOut( 350 ).attr( 'aria-hidden', 'true' );
                    var epoch_loaded = new Event('epoch_loaded');
                    document.dispatchEvent(epoch_loaded);
                });


        };


        /**
         * Get comment count
         *
         * @since 0.0.1
         *
         */
        app.comment_count = function( updateCheck ) {
            if( true === updateCheck ) {
                app.initial_load = false;
            }

            if ( null != epoch_vars.alt_comment_count ) {
                app.check_comment_count_from_file( updateCheck );
            }else{
                app.comment_count_from_wp( updateCheck );
            }

        };

        /**
         * Get comment count by querying WordPress
         *
         * @since 1.0.2
         */
        app.comment_count_from_wp = function( updateCheck ) {
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

                    if ( 'undefined' != response.count_total && 0 < response.count_total ) {
                        if ( updateCheck ) {
                            if ( response.count_approved > app.last_count ) {
                                app.new_comments();

                            }
                        }else{
                            app.get_comments();
                        }


                        app.set_last_count( response.count_approved );

                    } else {
                        app.no_comments = true;
                    }

                    app.shut_it_off = false;
                }
            );

        };

        /**
         * Get comment count by checking in file system. Will recheck via regualr query if file doesn't exist.
         *
         * @since 1.0.2
         */
        app.check_comment_count_from_file = function( updateCheck ) {
            app.shut_it_off = true;
            $.get(
                epoch_vars.alt_comment_count,{}
            ).fail( function () {
                    app.comment_count_from_wp( updateCheck );
                }
            ).success( function ( response ) {
                    if ( 'undefined' != response && 0 < response ) {
                        if ( updateCheck ) {
                            if ( response > app.last_count ) {
                                app.new_comments();

                            }
                        }else{
                            app.get_comments();
                        }


                        app.set_last_count( response );

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
                        parents.reverse();
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

                    if ( !is_new ) {
                        jQuery( 'body' ).triggerHandler( 'epoch.comments.loaded' );
                        app.comment_scroll();
                    }

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
         * Put a parse comment into the DOM
         *
         * @param html The actual HTML.
         * @param parent_id ID of parent, or 0 for top level comment.
         * @param level The threading level, not needed for top-level comments.
         */
        app.put_comment_in_dom = function( html, parent_id, level, id ) {
            if ( level > app.max_depth ) {
                level = app.max_depth;
            }

            if ( 0 == parent_id && 'DESC' == epoch_vars.epoch_options.order ) {
                first_child = app.comments_wrap_el.firstChild;
                new_el = document.createElement( 'div' );
                new_el.innerHTML = html;

                app.comments_wrap_el.insertBefore( new_el, first_child );
                if(  false === app.initial_load ) {
                    $( new_el ).children().find( 'article' ).addClass( 'epoch-success' ).delay( 750 ).queue(function(){
                        $( this ).removeClass( 'epoch-success' );
                    });
                }
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


                if(  false === app.initial_load ) {
                    var article = $( html ).find( 'article' ).attr( 'id' );
                    var a_el = document.getElementById( article );
                    $( a_el ).addClass( 'epoch-success' ).delay( 750 ).queue(function(){
                        $( this ).removeClass( 'epoch-success' );
                    });
                }


            }






            $( '.comment-reply-link' ).click( function( event ) {
                event.preventDefault();
            });
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
         * Scroll to comment hash
         *
         * @since 1.0.4
         */
        app.comment_scroll = function() {
            if ( jQuery( 'iframe#epoch-comments' ).length > 0 ) {
                return;
            }
            var location = "" + window.location;
            var pattern = /(#comment-\d+)/;
            if ( pattern.test( location ) ) {
                location = jQuery( "" + window.location.hash );
                if ( location.length > 0 ) {
                    var targetOffset = location.offset().top;
                    jQuery( 'html,body' ).animate( {scrollTop: targetOffset}, 1 );
                }
            }
        };


        app.set_last_count = function( count ) {
            app.last_count = count;
            $( app.count_el ).text( count );
        };

        app.set_comment_status = function( action, comment_id ) {
            //Action can be unapprove, approve, spam, trash
            $.post(
                epoch_vars.api_url, {
                    action: 'moderate_comments',
                    moderationAction: action,
                    epochNonce: epoch_vars.nonce,
                    postID: epoch_vars.post_id,
                    highest: app.highest_id,
                    commentID: comment_id
                } ).done( function( response  ) {

                    app.shut_it_off = false;

                } ).success( function( response ) {
                    response = app.get_data_from_response( response );
                    comment_id = response.comment_id;
                    status = response.status;
                    $comment = jQuery( '#div-comment-' + comment_id );
                    if( 'spam' == status || 'trash' == status ) {
                        $comment.fadeOut( 'slow' );
                        return;
                    }
                    comment = Epoch.parse_comment( response.comment );
                    jQuery( '#comment-' + comment_id ).replaceWith( comment );



                }

            );
            return false;
        }


    })( jQuery, window.Epoch || ( window.Epoch = {} ) );

} );

jQuery( function () {
    Epoch.init();

} );

