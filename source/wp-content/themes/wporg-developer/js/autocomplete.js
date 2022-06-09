/**
 * Autocomplete JS.
 *
 * Uses the Awesomplete widget from Lea Verou.
 * https://leaverou.github.io/awesomplete/
 */

( function( $ ) {

	if ( typeof autocomplete === 'undefined' ) {
		return;
	}

	var form = $( '.searchform' ).not(".searchform-handbook");
	if ( ! form.length ) {
		return;
	}

	var	searchfield = $( '#search-field', form ),
		processing = false,
		search = '',
		autocompleteResults = {};

	var awesome = new Awesomplete( searchfield.get( 0 ), {
		maxItems: 9999,
		minChars: 3,
		filter: function( text, input ) {
			// Filter autocomplete matches

			// Full match
			if ( Awesomplete.FILTER_CONTAINS( text, input ) ) {
				// mark
				return true;
			}

			// Replace - _ and whitespace with a single space
			var _text = Awesomplete.$.regExpEscape( text.trim().toLowerCase().replace( /[\_\-\s]+/g, ' ' ) );
			var _input = Awesomplete.$.regExpEscape( input.trim().toLowerCase().replace( /[\_\-\s]+/g, ' ' ) );

			// Matches with with single spaces between words
			if ( Awesomplete.FILTER_CONTAINS( _text, _input ) ) {
				return true;
			}

			_input = _input.split( " " );
			var words = _input.length;

			if ( 1 >= words ) {
				return false;
			}

			// Partial matches
			var partials = 0;
			for ( i = 0; i < words; i++ ) {
				if ( _text.indexOf( _input[ i ].trim() ) !== -1 ) {
					partials++;
				}
			}

			if ( partials === words ) {
				return true;
			}

			return false;
		},
		replace: function( text ) {
			searchfield.val( text );

			if ( text in autocompleteResults ) {
				window.location = autocompleteResults[ text ];
			}
		}
	} );

	// On input event for the search field.
	searchfield.on( 'input.autocomplete', function( e ) {

		// Update the autocomlete list: 
		//     if there are more than 2 characters
		//     and it's not already processing an Ajax request
		if ( !processing && $( this ).val().trim().length > 2 ) {
			search = $( this ).val();
			autocomplete_update();
		}
	} );


	/**
	 * Updates the autocomplete list
	 */
	function autocomplete_update() {

		processing = true;

		var data = {
			action: "autocomplete",
			data: form.serialize(),
			nonce: autocomplete.nonce,
		};

		$.post( autocomplete.ajaxurl, data )
			.done( function( response ) {

				if ( typeof response.success === 'undefined' ) {
					return false;
				}

				if ( typeof response.data === 'undefined' ) {
					return false;
				}

				if ( response.success === true && Object.values( response.data.posts ).length ) {
					autocompleteResults = response.data.posts;

					// Update the autocomplete list
					awesome.list = Object.keys( response.data.posts );
				}
			} )
			.always( function() {
				processing = false;

				// Check if the search was updated during processing
				if ( search !== searchfield.val() ) {
					searchfield.trigger( "input.autocomplete" );
				}
			} );
	}

} )( jQuery );