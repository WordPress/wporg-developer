const init = () => {
	const container = document.querySelector( '.wp-block-wporg-chapter-list > ul' );
	if ( container ) {
		container.parentNode.classList.toggle( 'has-js-control' );

		const parents = container.querySelectorAll( '.page_item_has_children' );
		parents.forEach( ( item ) => {
			// Get link, remove (will re-ad later).
			const link = item.querySelector( ':scope > a' );
			link.remove();

			// Get submenu
			const submenu = item.querySelector( ':scope > ul' );

			// Create the toggle button.
			const button = document.createElement( 'button' );
			button.setAttribute( 'aria-expanded', false );
			// button.setAttribute( 'aria-label', '' );
			button.onclick = () => {
				submenu.classList.toggle( 'is-open' );
				// This attribute returns a string.
				const isOpen = button.getAttribute( 'aria-expanded' );
				button.setAttribute( 'aria-expanded', isOpen === 'false' );
			};

			const buttonGroup = document.createElement( 'span' );
			buttonGroup.className = 'wporg-chapter-list--button-group';
			buttonGroup.append( button, link );

			item.insertBefore( buttonGroup, submenu );

			// Automatically open the trail to the current page.
			if (
				item.classList.contains( 'current_page_item' ) ||
				item.classList.contains( 'current_page_ancestor' )
			) {
				submenu.classList.toggle( 'is-open' );
				button.setAttribute( 'aria-expanded', true );
			}
		} );
	}
};

window.addEventListener( 'load', init );
