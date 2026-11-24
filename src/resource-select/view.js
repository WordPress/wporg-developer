const init = () => {
	const selector = document.querySelector( '#wp-block-wporg-resource-select' );

	if ( ! selector ) {
		return;
	}

	selector.addEventListener( 'change', ( event ) => {
		window.location = event.target.value;
	} );
};

window.addEventListener( 'load', init );
