/**
 * Preview for user contributed notes.
 *
 */

( function () {
	let textarea, preview, previewContent, tabs, processing, spinner;

	function init() {
		if ( typeof wporg_note_preview === 'undefined' ) {
			return;
		}

		textarea = document.querySelector( '.comment-form textarea' );
		preview = document.querySelector( '#comment-preview' );
		tabs = document.querySelectorAll( '#commentform .tablist a' );
		spinner = document.createElement( 'span' );
		spinner.className = 'spinner';
		spinner.style.display = 'none';
		processing = false;

		// Show tabs with JavaScript.
		document.querySelector( '#commentform .tablist' ).style.display = 'flex';

		if ( textarea && preview && tabs.length > 0 ) {
			// Append spinner to preview tab.
			tabs[ tabs.length - 1 ].parentNode.appendChild( spinner );

			previewContent = preview.querySelector( '.preview-content' );

			if ( previewContent ) {
				if ( ! textarea.value.length ) {
					previewContent.textContent = wporg_note_preview.preview_empty;
				}

				previewEvents();
			}
		}
	}

	function previewEvents() {
		const commentFormComment = document.getElementById( 'comment-form-comment' );
		const tabContentHeight = commentFormComment.offsetHeight;
		tabs.forEach( ( tab ) => {
			tab.addEventListener( 'keydown', handlePreviewEvent );
			tab.addEventListener( 'click', handlePreviewEvent );
		} );

		function handlePreviewEvent( e ) {
			// Preview tab should be at least as tall input tab to prevent resizing wonkiness.

			if ( tabContentHeight > 0 ) {
				preview.style.minHeight = `${ tabContentHeight }px`;
			}

			if ( this.getAttribute( 'aria-controls' ) === 'comment-preview' ) {
				if ( ! processing ) {
					const current_text = textarea.value.trim();
					if ( current_text.length && current_text !== wporg_note_preview.preview_empty ) {
						if ( wporg_note_preview.preview_empty === previewContent.textContent ) {
							// Remove "Nothing to preview" if there's new current text.
							previewContent.textContent = '';
						}
						// Update the preview.
						updatePreview( current_text );
					} else {
						previewContent.textContent = wporg_note_preview.preview_empty;
					}
				}

				// Remove outline from tab if clicked.
				if ( e.type === 'click' ) {
					this.blur();
				}
			} else {
				textarea.focus();
			}
		}
	}
	async function updatePreview( content ) {
		try {
			spinner.style.display = 'inline-block'; // Show spinner.
			processing = true;

			const params = new URLSearchParams();
			params.append( 'action', 'preview_comment' );
			params.append( 'preview_nonce', wporg_note_preview.nonce );
			params.append( 'preview_comment', content );
			const response = await fetch( wporg_note_preview.ajaxurl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: params,
			} );

			if ( ! response.ok ) {
				throw new Error( `HTTP error! status: ${ response.status }` );
			}

			const data = await response.json();
			updatePreview_HTML( data.data.comment );
		} catch ( error ) {
			console.error( 'Error:', error );
		} finally {
			spinner.style.display = 'none'; // Hide spinner.
			processing = false;

			// Make first child of the preview focusable.
			if ( preview.firstElementChild ) {
				preview.firstElementChild.setAttribute( 'tabindex', '0' );
			}
		}
	}

	function updatePreview_HTML( content ) {
		// Update preview content
		previewContent.innerHTML = content;

		// Hide spinner
		spinner.style.display = 'none';
	}

	init();
} )();
