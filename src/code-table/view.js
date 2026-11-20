const init = () => {
	const tables = document.querySelectorAll( '.wp-block-wporg-code-table' );
	if ( tables ) {
		tables.forEach( ( table ) => {
			const showMoreLink = table.querySelector( '.wp-block-wporg-code-table-show-more' );
			const showLessLink = table.querySelector( '.wp-block-wporg-code-table-show-less' );

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
				} );
			}
		} );
	}
};

window.addEventListener( 'load', init );
