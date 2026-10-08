const init = () => {
	const allButton = document.querySelector( '#wp-block-wporg-search-filters-all' );
	const container = document.querySelector( '.wp-block-wporg-search-filters' );

	if ( ! allButton || ! container ) {
		return;
	}

	const inputs = container.querySelectorAll( 'input[type="checkbox"]' );

	const hasFilterChecked = () => [ ...inputs ].some( ( input ) => input.checked );

	const updateAllButtonState = () => {
		allButton.setAttribute( 'aria-pressed', ! hasFilterChecked() );
	};

	allButton.addEventListener( 'click', ( event ) => {
		event.preventDefault();
		inputs.forEach( ( input ) => {
			input.checked = false;
		} );
		updateAllButtonState();
	} );

	container.addEventListener( 'change', () => {
		updateAllButtonState();
	} );

	updateAllButtonState();
};

window.addEventListener( 'load', init );
