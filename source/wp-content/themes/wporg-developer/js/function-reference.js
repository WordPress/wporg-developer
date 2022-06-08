/* global jQuery, wporgFunctionReferenceI18n */
/**
 * function-reference.js
 *
 * Handles all interactivity on the single function page
 */

jQuery( function ( $ ) {
	let $usesList, $usedByList, $showMoreUses, $hideMoreUses, $showMoreUsedBy, $hideMoreUsedBy;
	let $sourceCollapsedHeight;

	function onLoad() {
		sourceCodeHighlightInit();

		toggleUsageListInit();
	}

	function sourceCodeHighlightInit() {
		// We require the Prism javascript library
		if ( undefined === window.Prism ) {
			return;
		}

		// 1em (margin) + 10 * 17px + 10. Lines are 1.1em which rounds to 17px: calc( 1em + 17px * 10 + 10 ).
		// Extra 10px added to partially show next line so it's clear there is more.
		$sourceCollapsedHeight = 196;
		sourceCodeDisplay();
	}

	function sourceCodeDisplay( element ) {
		let sourceCode = [];
		if ( element !== undefined ) {
			// Find table inside a specific source code element if passed.
			sourceCode = $( '.source-content', element ).find( 'pre' );
		} else {
			// Find table inside all source code elements.
			sourceCode = $( '.source-content' ).find( 'pre' );
		}

		if ( ! sourceCode.length ) {
			return;
		}

		sourceCode.each( function () {
			if ( $sourceCollapsedHeight - 12 < $( this ).height() ) {
				const sourceContent = $( this ); //.closest( '.wp-block-code' );

				// Do this with javascript so javascript-less can enjoy the total sourcecode
				sourceContent.css( {
					height: $sourceCollapsedHeight + 'px',
				} );

				sourceContent.next( '.source-code-links' ).find( 'span:first' ).show();
				sourceContent.parent().find( '.show-complete-source' ).show();
				sourceContent
					.parent()
					.find( '.show-complete-source' )
					.off( 'click.togglesource' )
					.on( 'click.togglesource', toggleCompleteSource );
				sourceContent
					.parent()
					.find( '.less-complete-source' )
					.off( 'click.togglesource' )
					.on( 'click.togglesource', toggleCompleteSource );
			}
		} );
	}

	function toggleCompleteSource( event ) {
		event.preventDefault();

		const sourceContent = $( this ).closest( '.source-content' ).find( 'pre' );
		let heightGoal = 0;

		if ( $( this ).parent().find( '.show-complete-source' ).is( ':visible' ) ) {
			heightGoal = sourceContent.find( 'code' ).height() + 45; // takes into consideration potential x-scrollbar
		} else {
			heightGoal = $sourceCollapsedHeight;
		}

		sourceContent.animate( { height: heightGoal + 'px' } );

		$( this ).parent().find( 'a' ).toggle();
	}

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

	// Inject the "copy" button into every code block.
	$( '.wp-block-code' ).each( function ( i, element ) {
		const $element = $( element );
		let timeoutId;

		const $button = $( document.createElement( 'button' ) );
		$button.text( wporgFunctionReferenceI18n.copy );
		$button.on( 'click', function () {
			clearTimeout( timeoutId );
			const code = $element.find( 'code' ).text();
			if ( ! code ) {
				return;
			}

			// This returns a promise which will resolve if the copy suceeded,
			// and we can set the button text to tell the user it worked.
			// We don't do anything if it fails.
			window.navigator.clipboard.writeText( code ).then( function () {
				$button.text( wporgFunctionReferenceI18n.copied );
				wp.a11y.speak( wporgFunctionReferenceI18n.copied );

				// After 5 seconds, reset the button text.
				timeoutId = setTimeout( function () {
					$button.text( wporgFunctionReferenceI18n.copy );
				}, 5000 );
			} );
		} );

		$element.prepend( $button );
	} );

	$( onLoad );
} );
