/**
 * Autocomplete JS.
 *
 * Uses the Awesomplete widget from Lea Verou.
 * https://leaverou.github.io/awesomplete/
 */

function init() {
	/** @type {string} */
	const stringifiedData = document.getElementById(
		'wp-script-module-data-@wporg-developer/autocomplete'
	)?.textContent;

	let data;
	try {
		data = JSON.parse( stringifiedData );
	} catch {
		// No data from server, nothing to do.
		return;
	}

	if ( typeof window.Awesomplete === 'undefined' ) {
		return;
	}

	const form = document.querySelector( '#wporg-search .wp-block-search' );
	if ( ! form ) {
		return;
	}

	/** @type {HTMLInputElement} */
	const searchfield = form.querySelector( 'input[type="search"]' );
	if ( ! searchfield ) {
		return;
	}

	let processing = false,
		search = '',
		autocompleteResults = {};

	const awesome = new Awesomplete( searchfield, {
		maxItems: 9999,
		minChars: 3,
		filter: function ( text, input ) {
			// Filter autocomplete matches

			// Full match
			if ( Awesomplete.FILTER_CONTAINS( text, input ) ) {
				// mark
				return true;
			}

			// Replace - _ and whitespace with a single space
			var _text = Awesomplete.$.regExpEscape(
				text
					.trim()
					.toLowerCase()
					.replace( /[\_\-\s]+/g, ' ' )
			);
			var _input = Awesomplete.$.regExpEscape(
				input
					.trim()
					.toLowerCase()
					.replace( /[\_\-\s]+/g, ' ' )
			);

			// Matches with with single spaces between words
			if ( Awesomplete.FILTER_CONTAINS( _text, _input ) ) {
				return true;
			}

			_input = _input.split( ' ' );
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
		replace: function ( text ) {
			searchfield.val( text );

			if ( text in autocompleteResults ) {
				window.location = autocompleteResults[ text ];
			}
		},
	} );

	// On input event for the search field.
	searchfield.addEventListener( 'input', function ( e ) {
		// Update the autocomplete list:
		//     if there are more than 2 characters
		//     and it's not already processing an Ajax request
		if ( ! processing && this.value.trim().length > 2 ) {
			search = this.value;
			autocomplete_update();
		}
	} );

	console.log( data, searchfield );

	/**
	 * Updates the autocomplete list
	 */
	async function autocomplete_update() {
		processing = true;

		const url = new URL( data.rest_url, document.location.href );

		const requestData = { s: '', post_types: [], nonce: data.nonce };
		requestData.s = form.elements.namedItem( 's' )?.value ?? '';
		for ( const postTypeEl of form.elements.namedItem( 'post_type[]' ) ) {
			if ( postTypeEl.checked ) {
				requestData.post_types.push( postTypeEl.value );
			}
		}

		console.log( requestData );

		/** @type {Response} */
		let response;
		try {
			response = await fetch( url, {
				method: 'POST',
				body: JSON.stringify( requestData ),
				headers: { 'Content-Type': 'application/json' },
			} );
			if ( ! response.ok ) {
				throw new Error( 'Response not OK' );
			}
			response = await response.json();
		} finally {
			processing = false;
		}

		console.log( response );

		//$.post( autocomplete.ajaxurl, data )
		//	.done( function ( response ) {
		//		if ( typeof response.success === 'undefined' ) {
		//			return false;
		//		}
		//
		//		if ( typeof response.data === 'undefined' ) {
		//			return false;
		//		}
		//
		//		if ( response.success === true && Object.values( response.data.posts ).length ) {
		//			autocompleteResults = response.data.posts;
		//
		//			// Update the autocomplete list
		//			awesome.list = Object.keys( response.data.posts );
		//		}
		//	} )
		//	.always( function () {
		//		processing = false;
		//
		//		// Check if the search was updated during processing
		//		if ( search !== searchfield.val() ) {
		//			searchfield.trigger( 'input.autocomplete' );
		//		}
		//	} );
	}
}

init();
