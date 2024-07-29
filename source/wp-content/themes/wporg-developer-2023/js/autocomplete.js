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

	const form = document.querySelector( '#wporg-search .wp-block-search' );
	if ( ! form ) {
		return;
	}

	/** @type {HTMLInputElement} */
	const searchfield = form.querySelector( 'input[type="search"]' );
	if ( ! searchfield ) {
		return;
	}

	let processing = false;
	let search = '';
	let autocompleteResults = new Map();

	let awesome;

	async function createAwesomplete() {
		const Awesomplete = ( await import( '@wporg-developer/awesomplete' ) ).Awesomplete;
		return new Awesomplete( searchfield, {
			maxItems: 9999,
			minChars: 3,
			filter: ( text, input ) => {
				// Filter autocomplete matches

				// Full match
				if ( Awesomplete.FILTER_CONTAINS( text, input ) ) {
					// mark
					return true;
				}

				// Replace - _ and whitespace with a single space
				const _text = Awesomplete.$.regExpEscape(
					text
						.trim()
						.toLowerCase()
						.replace( /[\_\-\s]+/g, ' ' )
				);
				const _input = Awesomplete.$.regExpEscape(
					input
						.trim()
						.toLowerCase()
						.replace( /[\_\-\s]+/g, ' ' )
				);

				// Matches with with single spaces between words
				if ( Awesomplete.FILTER_CONTAINS( _text, _input ) ) {
					return true;
				}

				const words = _input.split( ' ' ).length;

				if ( 1 >= words ) {
					return false;
				}

				// Partial matches
				let partials = 0;
				for ( let i = 0; i < words; i++ ) {
					if ( _text.indexOf( _input[ i ].trim() ) !== -1 ) {
						partials++;
					}
				}

				if ( partials === words ) {
					return true;
				}

				return false;
			},
			replace: ( text ) => {
				searchfield.val( text );

				if ( autocompleteResults.has( text ) ) {
					window.location = autocompleteResults.get( text );
				}
			},
		} );
	}

	// On input event for the search field.
	searchfield.addEventListener(
		'input',
		async function () {
			const el = this;
			// Update the autocomplete list:
			//     if there are more than 2 characters
			//     and it's not already processing an Ajax request
			if ( ! processing && this.value.trim().length > 2 ) {
				search = this.value;
				await autocomplete_update( el );
			}
		},
		{ passive: true }
	);

	/**
	 * Updates the autocomplete list
	 */
	async function autocomplete_update( el ) {
		if ( processing ) {
			return;
		}

		if ( ! awesome ) {
			// When the autocomplete is created, the node is moved and looses focus.
			// Restore it.
			awesome = await createAwesomplete();
			el.focus();
		}

		processing = true;

		const url = new URL( data.endpoint_url );

		const requestData = {
			search: form.elements.namedItem( 's' )?.value ?? '',
			post_types: [],
			nonce: data.nonce,
		};
		for ( const postTypeEl of form.elements.namedItem( 'post_type[]' ) ) {
			if ( postTypeEl.checked ) {
				requestData.post_types.push( postTypeEl.value );
			}
		}

		/** @type {Response} */
		let response;
		try {
			response = await fetch( url, {
				method: 'POST',
				body: JSON.stringify( requestData ),
				headers: { 'Content-Type': 'application/json' },
			} );
			if ( ! response.ok ) {
				throw new Error( 'Bad response.' );
			}
			response = await response.json();
		} finally {
			processing = false;
		}

		/** @type {Record<string,string>|undefined} */
		const posts = response.posts;
		if ( ! posts ) {
			return;
		}

		autocompleteResults = new Map( Object.entries( posts ) );
		awesome.list = Object.keys( posts );
	}
}

init();
