const init = () => {
	const tabLists = document.querySelectorAll( '.code-tabs' );

	if ( ! tabLists.length ) {
		return;
	}

	tabLists.forEach( ( tabList ) => {
		const buttons = tabList.querySelectorAll( '.code-tab' );
		const panels = tabList.querySelectorAll( '.code-tab-block' );

		buttons.forEach( ( button ) => {
			const isActive = button.classList.contains( 'is-active' );
			const buttonId = window.crypto?.randomUUID?.();
			const panelId = window.crypto?.randomUUID?.();
			const tabName = button.getAttribute( 'data-language' );
			const relatedPanel = Array.from( panels ).find( ( panel ) => panel.classList.contains( tabName ) );

			button.classList.add( 'wp-block-button__link' );
			button.setAttribute( 'role', 'tab' );
			button.setAttribute( 'aria-selected', isActive );
			button.setAttribute( 'tabindex', isActive ? '0' : '-1' );
			button.setAttribute( 'id', buttonId );

			if ( buttonId && panelId && relatedPanel ) {
				button.setAttribute( 'aria-controls', panelId );
				relatedPanel.setAttribute( 'aria-labelledby', buttonId );
				relatedPanel.setAttribute( 'id', panelId );
			}
		} );

		panels.forEach( ( panel ) => {
			panel.setAttribute( 'role', 'tabpanel' );
		} );

		tabList.setAttribute( 'role', 'tablist' );
		// Block button styles require a parent with `class*="wp-block"`.
		tabList.classList.add( 'wp-block', 'is-small', 'is-style-toggle' );

		tabList.addEventListener( 'click', ( event ) => {
			event.preventDefault();

			if ( Array.from( buttons ).includes( event.target ) ) {
				const targetButton = event.target;
				const id = targetButton.getAttribute( 'aria-controls' );

				if ( targetButton.classList.contains( 'is-active' ) ) {
					return;
				}

				// Update button states.
				buttons.forEach( ( button ) => {
					button.classList.remove( 'is-active' );
					button.setAttribute( 'aria-selected', 'false' );
					button.setAttribute( 'tabindex', '-1' );
				} );
				targetButton.classList.add( 'is-active' );
				targetButton.setAttribute( 'aria-selected', 'true' );
				targetButton.setAttribute( 'tabindex', '0' );

				// Update tab states.
				panels.forEach( ( panel ) => {
					panel.classList.remove( 'is-active' );
				} );
				document.getElementById( id )?.classList.add( 'is-active' );
			}
		} );
	} );
};

window.addEventListener( 'load', init );
