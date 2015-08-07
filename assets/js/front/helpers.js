/**
 * Handlebars helper for link markup
 *
 * Usage: {{epochLink http://hats.com 'hats' 'Link to hats' 'link-class' '_blank' 'nofollow'}}
 *
 * Note: text and title args can be properties of epoch_translation object to use a translation string
 */
Handlebars.registerHelper( 'epochLink', function( url, text, title, class_attr, target, rel ) {
    text = text.replace(/-comment_date-/g, this.comment_date );

    if  ( text in epoch_translation ) {
        text = epoch_translation[ text ];

    }

    if ( title in epoch_translation ) {
        title = epoch_translation[ title ];
    }

    text = new Handlebars.SafeString( text );
    if (  ! url || /^\s*$/.test( url ) ) {
        url = '#';
    }else{
        url  = Handlebars.Utils.escapeExpression( url );
    }

    title  = Handlebars.Utils.escapeExpression( title );

    var result = '<a href="' + url + '" title="' + title + '"';

    if (  ! rel || /^\s*$/.test( rel )  ) {
        //make a default?

    }else{
        rel = Handlebars.Utils.escapeExpression( rel );
        result += ' rel="' + rel + '"';
    }

    if ( 'string' != typeof target || ! target || /^\s*$/.test( target )  ) {
        //make a default?

    }else{
        target = Handlebars.Utils.escapeExpression( target );
        result += ' target="' + target + '"';
    }

    if ( 'string' != typeof class_attr || ! class_attr || /^\s*$/.test( class_attr )  ) {
        //make a default?

    }else{
        class_attr = Handlebars.Utils.escapeExpression( class_attr );
        result += ' class="' + class_attr + '"';
    }

    result +=   ' >' + text + '</a>';

    return new Handlebars.SafeString( result );

});

/**
 * Output a translatable string from the epoch_translation object
 *
 * {{epochTranslation 'awaiting_moderation'}}
 */
Handlebars.registerHelper( 'epochTranslation', function( slug ) {
    string = '';
    if ( slug in epoch_translation ) {
        string = epoch_translation[ slug ];
    }

    return new Handlebars.SafeString( string );

});
