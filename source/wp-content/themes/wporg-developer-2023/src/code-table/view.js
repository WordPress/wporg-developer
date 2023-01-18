/**
 * WordPress dependencies
 */

const init = () => {
	const tables = document.querySelectorAll( '.wp-block-wporg-code-table' );
	if ( tables ) {
		tables.forEach( ( container ) => {
			const showMoreLink = container.querySelector( '.wp-block-wporg-code-table-show-more' );
			const showLessLink = container.querySelector( '.wp-block-wporg-code-table-show-less' );

			showMoreLink.addEventListener( 'click', ( event ) => {
				event.preventDefault();
				container.classList.add( 'expanded' );
			} );

			showLessLink.addEventListener( 'click', ( event ) => {
				event.preventDefault();
				container.classList.remove( 'expanded' );
			} );
		} );
	}
};

window.addEventListener( 'load', init );
