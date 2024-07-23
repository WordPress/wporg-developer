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
		wpAdminBar = document.querySelector( '#wpadminbar' ).length ? 32 : 0;
		// Display form and scroll to it
		if ( '#respond' === window.location.hash ) {
			showCommentForm();
		}
		if ( ! wpAdminBar || ! commentID ) {
			return;
		}
		const comment = document
			.querySelector( '#comments' )
			.find( commentID + '.depth-1' )
			.first();
		if ( ! comment.length ) {
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
	document.querySelectorAll( '#comments .comment-date' ).forEach( ( element ) => {
		element.addEventListener( 'click', function () {
			// Scroll to comment and adjust for admin bar.
			// Add 16px for child comments.
			const pos = this.getBoundingClientRect();
			const offsetTop = pos.top + window.scrollY;
			window.scrollTo( {
				top: offsetTop - wpAdminBar - 16,
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

	if ( ! commentForm.length ) {
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
		const element_target = e.target;
		let scroll, sel;

		if ( e.key === 'Escape' ) {
			console.log( 'Escape' );
			// When pressing Escape: Opera 12 and 27 blur form fields, IE 8 clears them.
			e.preventDefault();
			element_target.setAttribute( 'data-tab-out', 'true' );
			return;
		}

		if ( e.key !== 'Tab' || e.ctrlKey || e.altKey || e.shiftKey ) return;

		if ( element_target.getAttribute( 'data-tab-out' ) === 'true' ) {
			element_target.setAttribute( 'data-tab-out', 'false' );
			return;
		}

		const selStart = element_target.selectionStart;
		const selEnd = element_target.selectionEnd;
		const val = element_target.value;

		if ( document.selection ) {
			element_target.focus();
			sel = document.selection.createRange();
			sel.text = '\t';
		} else if ( selStart >= 0 ) {
			scroll = element_target.scrollTop;
			element_target.value = val.substring( 0, selStart ).concat( '\t', val.substring( selEnd ) );
			element_target.selectionStart = element_target.selectionEnd = selStart + 1;
			element_target.scrollTop = scroll;
		}

		if ( e.stopPropagation ) e.stopPropagation();
		if ( e.preventDefault ) e.preventDefault();
	} );
} )();
