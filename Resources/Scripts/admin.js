(function( jQuery ) {

    /**
     * This will activate the plugin by hitting the activate-plugin ajax endpoint.
     *
     * @param activateUrl This is the URL that was provided by the install-plugin endpoint.
     */
    function optimizer_activate_plugin( activateUrl ) {
        jQuery.ajax({
            url: activateUrl,
            type: 'POST',
            success: function( response ) {
                location.reload();
            },
            error: function( response ) {
                alert( "Unable to activate the plugin." );
            }
        });
    }

    /**
     * This will install a plugin by sending the plugins SLUG to WordPress' install-plugin endpoint.
     * When the plugin has been installed, it will then attempt to activate it.
     *
     * @param slug The key that identifies which plugin we want to install from the WP Repository.
     * @param admin_ajax The URL for the administration ajax page.
     * @param nonce The security nonce that allows wordpress to know this is a valid request.
     */
    function optimizer_install_plugin( slug, admin_ajax, nonce ) {
        jQuery.ajax({
            url: admin_ajax,
            type: 'POST',
            data: {
                slug: slug,
                action: 'install-plugin',
                _wpnonce: nonce
            },
            success: function( response ) {
                if ( response.success ) {
                    optimizer_activate_plugin( response.data.activateUrl );
                } else {
                    alert( "Unable to install the plugin." );
                }
            },
            error: function( response ) {
                alert( "Unable to install the plugin." );
            }
        });
    }

    /**
     * This is the part where we make modifications to the DOM (Document Object Model)
     * after it has been loaded. We can attach our event handlers here, along with
     * activating any jQuery/Javascript plugins that will change the DOM. */
    jQuery( document ).ready(function() {
        jQuery( '.delete-revision' ).click( function() {
            var title = jQuery( this ).attr( 'data-title' );
            return confirm( title );
        });

        jQuery('.install-plugin').click(function(){
            var slug       = jQuery(this).attr('data-slug');
            var admin_ajax = jQuery('#introduction').attr('data-url');
            var nonce      = jQuery('#introduction').attr('data-nonce');

            jQuery('.install-plugin').attr('disabled', 'disabled');
            jQuery('#' + slug).find('input').val('Installing Plugin');

            optimizer_install_plugin( slug, admin_ajax, nonce );
        });
    });

})(jQuery);