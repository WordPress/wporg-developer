/* @global jQuery */
/**
 * function-reference.js
 *
 * Handles all interactivity on the single function page
 */

jQuery( function( $ ) {
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
