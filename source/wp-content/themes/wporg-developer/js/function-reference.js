/* global jQuery, wporgFunctionReferenceI18n */
/**
 * function-reference.js
 *
 * Handles all interactivity on the single function page
 */

// eslint-disable-next-line id-length -- $ OK.
jQuery( function ( $ ) {
	// 22.5px (line height) * 10 for 10 lines + 15px top padding + 10px extra.
	// The extra 10px added to partially show next line so it's clear there is more.
	const MIN_HEIGHT = 22.5 * 10 + 15 + 10;

	function collapseCodeBlock( $element, $button ) {
		$button.text( wporgFunctionReferenceI18n.expand );
		$element.data( 'is-expanded', false );
		$element.css( { height: MIN_HEIGHT + 'px' } );
	}

	function expandCodeBlock( $element, $button ) {
		$button.text( wporgFunctionReferenceI18n.collapse );
		$element.data( 'is-expanded', true );
		$element.css( { height: $element.data( 'height' ) + 'px' } );
	}

	// For each code block, add the copy button & expanding functionality.
	$( '.wp-block-code' ).each( function ( i, element ) {
		const $element = $( element );
		let timeoutId;

		const $copyButton = $( document.createElement( 'button' ) );
		$copyButton.text( wporgFunctionReferenceI18n.copy );
		$copyButton.on( 'click', function () {
			clearTimeout( timeoutId );
			const code = $element.find( 'code' ).text();
			if ( ! code ) {
				return;
			}

			// This returns a promise which will resolve if the copy suceeded,
			// and we can set the button text to tell the user it worked.
			// We don't do anything if it fails.
			window.navigator.clipboard.writeText( code ).then( function () {
				$copyButton.text( wporgFunctionReferenceI18n.copied );
				wp.a11y.speak( wporgFunctionReferenceI18n.copied );

				// After 5 seconds, reset the button text.
				timeoutId = setTimeout( function () {
					$copyButton.text( wporgFunctionReferenceI18n.copy );
				}, 5000 );
			} );
		} );

		const $container = $( document.createElement( 'div' ) );
		$container.addClass( 'wp-code-block-button-container' );

		$container.append( $copyButton );

		// Check code block height, and if it's larger, add in the collapse
		// button, and set it to be collapsed differently.
		const originalHeight = $element.height();
		if ( originalHeight > MIN_HEIGHT ) {
			$element.data( 'height', originalHeight );

			const $expandButton = $( document.createElement( 'button' ) );
			$expandButton.addClass( 'button-link' );
			$expandButton.on( 'click', function () {
				if ( $element.data( 'is-expanded' ) ) {
					collapseCodeBlock( $element, $expandButton );
				} else {
					expandCodeBlock( $element, $expandButton );
				}
			} );

			collapseCodeBlock( $element, $expandButton );
			$container.append( $expandButton );
		}

		$element.prepend( $container );
	} );

	let $usesList, $usedByList, $showMoreUses, $hideMoreUses, $showMoreUsedBy, $hideMoreUsedBy;

	function toggleUsageListInit() {
		// We only expect one used_by and uses per document
		$usedByList = $( 'tbody tr', '#used-by-table' );
		$usesList = $( 'tbody tr', '#uses-table' );

		if ( $usedByList.length > 5 ) {
			$usedByList = $usedByList.slice( 5 ).hide();

			$showMoreUsedBy = $( '.used-by .show-more' ).show().on( 'click', toggleMoreUsedBy );
			$hideMoreUsedBy = $( '.used-by .hide-more' ).on( 'click', toggleMoreUsedBy );
		}

		if ( $usesList.length > 5 ) {
			$usesList = $usesList.slice( 5 ).hide();

			$showMoreUses = $( '.uses .show-more' ).show().on( 'click', toggleMoreUses );
			$hideMoreUses = $( '.uses .hide-more' ).on( 'click', toggleMoreUses );
		}
	}

	function toggleMoreUses( event ) {
		event.preventDefault();

		$usesList.toggle();

		$showMoreUses.toggle();
		$hideMoreUses.toggle();
	}

	function toggleMoreUsedBy( event ) {
		event.preventDefault();

		$usedByList.toggle();

		$showMoreUsedBy.toggle();
		$hideMoreUsedBy.toggle();
	}

	toggleUsageListInit();
} );
