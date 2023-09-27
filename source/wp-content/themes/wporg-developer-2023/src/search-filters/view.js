const init = () => {
	const allButton = document.querySelector( '#wp-block-wporg-search-filters-all' );
	const container = document.querySelector( '.wp-block-wporg-search-filters' );

	if ( ! allButton || ! container ) {
		return;
	}

	const inputs = container.querySelectorAll( 'input[type="checkbox"]' );

	const hasFilterChecked = () => [ ...inputs ].some( ( input ) => input.checked );

	const updateButtonState = () => {
		allButton.setAttribute( 'aria-pressed', ! hasFilterChecked() );
	};

	allButton.addEventListener( 'click', ( event ) => {
		event.preventDefault();
		inputs.forEach( ( input ) => {
			input.checked = false;
		} );
		updateButtonState();
	} );

	container.addEventListener( 'change', () => {
		updateButtonState();
	} );

	updateButtonState();
};

window.addEventListener( 'load', init );
