/**
 * Dynamic functionality for voting on user submitted notes.
 *
 * @param {Object} wp The WordPress JavaScript object.
 */

( function ( wp ) {
	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelector( '.comment-list' ).addEventListener( 'click', function ( event ) {
			if ( event.target.matches( 'a.user-note-voting-up, a.user-note-voting-down' ) ) {
				event.preventDefault();

				const item = event.target;
				const comment = item.closest( '.comment' );

				const params = new URLSearchParams();
				params.append( 'action', 'note_vote' );
				params.append( 'comment', item.getAttribute( 'data-id' ) );
				params.append( 'vote', item.getAttribute( 'data-vote' ) );
				params.append( '_wpnonce', item.parentNode.getAttribute( 'data-nonce' ) );

				fetch( wporg_note_voting.ajaxurl, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					},
					body: params,
				} )
					.then( ( response ) => response.text() )
					.then( ( data ) => {
						if ( data !== '0' ) {
							item.closest( '.user-note-voting' ).outerHTML = data;
							wp.a11y.speak( comment.querySelector( '.user-note-voting-count' ).textContent );
						}
					} );

				return false;
			}
		} );
	} );
} )( window.wp );
