/**
 * Dynamic functionality for comments as user submitted notes.
 *
 */

( function () {
	const commentForm = document.querySelector( '.comment-form textarea' );
	let commentID = window.location.hash;
	let wpAdminBar = 0;

	// Check if the fragment identifier is a comment ID (e.g. #comment-63).
	if ( ! commentID.match( /#comment\-[0-9]+$/ ) ) {
		commentID = '';
	}

	// Actions for when the page is ready.
	document.addEventListener( 'DOMContentLoaded', function () {
		// Set wpAdminBar.
		wpAdminBar =
			! document.querySelector( '#wpadminbar' ) || document.querySelector( '#wpadminbar' ).length ? 32 : 0;
		// Display form and scroll to it
		if ( '#respond' === window.location.hash ) {
			showCommentForm();
		}
		if ( ! wpAdminBar || ! commentID ) {
			return;
		}
		const comment = document.querySelector( `.comment-list ${ commentID } .depth-1` );
		if ( ! comment || ! comment.length ) {
			return;
		}
		// Scroll to top level comment and adjust for admin bar.
		const pos = comment.getBoundingClientRect();
		window.scrollTo( {
			top: window.scrollY + pos.top - wpAdminBar,
			behavior: 'smooth',
		} );
	} );

	// Scroll to comment if comment date link is clicked.
	document.querySelectorAll( '.comment-list .comment-date' ).forEach( ( element ) => {
		element.addEventListener( 'click', function ( event ) {
			// Scroll to comment and adjust for admin bar.
			// Add 16px for child comments and 60px for the new header.
			event.preventDefault();
			const pos = this.getBoundingClientRect();
			const offsetTop = pos.top + window.scrollY;
			window.scrollTo( {
				top: offsetTop - wpAdminBar - 76,
				behavior: 'smooth',
			} );
		} );
	} );

	function showCommentForm() {
		const target = document.querySelector( '#commentform #add-note-or-feedback' );
		if ( target ) {
			const pos = target.getBoundingClientRect();
			const offsetTop = pos.top + window.scrollY;
			window.scrollTo( {
				top: offsetTop - wpAdminBar,
				behavior: 'smooth',
			} );

			document.querySelector( '.wp-editor-area' ).focus();
		}
	}

	if ( ! commentForm || ! commentForm.length ) {
		return;
	}

	document.querySelectorAll( '.table-of-contents a[href="#add-note-or-feedback"]' ).forEach( ( element ) => {
		element.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			showCommentForm();
		} );
	} );

	// Add php and js buttons to QuickTags.
	QTags.addButton( 'php', 'php', '[php]', '[/php]', '', '', '', 'comment' );
	QTags.addButton( 'js', 'js', '[js]', '[/js]', '', '', '', 'comment' );
	QTags.addButton( 'inline-code', 'inline code', '<code>', '</code>', '', '', '', 'comment' );

	// Override tab within user notes textarea to actually insert a tab character.
	// Copied from code within core's wp-admin/js/common.js.
	commentForm.bind( 'keydown.wpevent_InsertTab', function ( e ) {
		const elementTarget = e.target;
		let scroll, sel;

		if ( e.key !== 'Tab' || e.ctrlKey || e.altKey || e.shiftKey ) return;

		if ( elementTarget.getAttribute( 'data-tab-out' ) === 'true' ) {
			elementTarget.setAttribute( 'data-tab-out', 'false' );
			return;
		}

		const selStart = elementTarget.selectionStart;
		const selEnd = elementTarget.selectionEnd;
		const val = elementTarget.value;

		if ( document.selection ) {
			elementTarget.focus();
			sel = document.selection.createRange();
			sel.text = '\t';
		} else if ( selStart >= 0 ) {
			scroll = elementTarget.scrollTop;
			elementTarget.value = val.substring( 0, selStart ).concat( '\t', val.substring( selEnd ) );
			elementTarget.selectionStart = elementTarget.selectionEnd = selStart + 1;
			elementTarget.scrollTop = scroll;
		}

		if ( e.stopPropagation ) e.stopPropagation();
		if ( e.preventDefault ) e.preventDefault();
	} );
} )();
