jQuery( function() {
	jQuery( 'a.remove-publish-handler' ).click( function( e ) {
		e.preventDefault();
		var link = jQuery( this );
		jQuery( 'input.remove-handler-id', link.parents( 'div.handlers-wrapper' ) ).val( link.attr( 'rel' ) );
		jQuery( 'input.do-remove-handler', link.parents( 'div.handlers-wrapper' ) ).trigger( 'click' );
	} );
	jQuery( 'a.remove-publish-handler-class-attribute' ).click( function( e ) {
		e.preventDefault();
		var link = jQuery( this );
		jQuery( 'input.remove-handler-class-attribute-id', link.parents( 'div.handlers-wrapper' ) ).val( link.attr( 'rel' ) );
		jQuery( 'input.do-remove-handler-class-attribute', link.parents( 'div.handlers-wrapper' ) ).trigger( 'click' );
	} );
} );