const init = () => {
	const tabs = document.querySelectorAll( '.code-tabs' );

	if ( ! tabs.length ) {
		return;
	}

	tabs.forEach( ( tab ) => {
		const buttons = tab.querySelectorAll( 'button' );

		buttons.forEach( ( button ) => {
			button.classList.add( 'wp-block-button__link' );

			if ( button.classList.contains( 'is-active' ) ) {
				button.setAttribute( 'aria-pressed', 'true' );
			}
		} );

		// Block button styles require a parent with `class*="wp-block"`.
		tab.classList.add( 'wp-block', 'is-small', 'is-style-toggle' );

		tab.addEventListener( 'click', ( event ) => {
			event.preventDefault();

			if ( event.target.classList.contains( 'wp-block-button__link' ) ) {
				const targetButton = event.target;
				const tabName = targetButton.getAttribute( 'data-language' );

				if ( targetButton.classList.contains( 'is-active' ) ) {
					return;
				}

				// Update button states.
				buttons.forEach( ( button ) => {
					button.classList.remove( 'is-active' );
					button.setAttribute( 'aria-pressed', 'false' );
				} );
				targetButton.classList.add( 'is-active' );
				targetButton.setAttribute( 'aria-pressed', 'true' );

				// Update tab states.
				tab.querySelectorAll( '.code-tab-block' ).forEach( ( block ) => {
					block.classList.remove( 'is-active' );
				} );
				tab.querySelector( `.code-tab-block[class*="${ tabName }"]` ).classList.add( 'is-active' );
			}
		} );
	} );
};

window.addEventListener( 'load', init );
