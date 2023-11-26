const init = () => {
	const tables = document.querySelectorAll( '.wp-block-wporg-code-table' );
	if ( tables ) {
		tables.forEach( ( table ) => {
			const showMoreLink = table.querySelector( '.wp-block-wporg-code-table-show-more' );
			const showLessLink = table.querySelector( '.wp-block-wporg-code-table-show-less' );
			const sidebarContainer = document.querySelector( '.wp-block-wporg-sidebar-container' );

			if ( showMoreLink ) {
				showMoreLink.addEventListener( 'click', ( event ) => {
					event.preventDefault();
					table.classList.add( 'expanded' );
				} );
			}

			if ( showLessLink ) {
				showLessLink.addEventListener( 'click', ( event ) => {
					event.preventDefault();
					table.classList.remove( 'expanded' );
					if ( sidebarContainer && sidebarContainer.classList.contains( 'is-bottom-sidebar' ) ) {
						// Ensures that the sidebar repositions correctly after collapsing.
						// It addresses the issue where the sidebar stays at its expanded position
						// due to retaining the 'is-bottom-sidebar' class and the 'top' property post-collapse.
						sidebarContainer.classList.remove( 'is-bottom-sidebar' );
						sidebarContainer.style.removeProperty( 'top' );
					}
				} );
			}
		} );
	}
};

window.addEventListener( 'load', init );
