const init = () => {
	const tabLists = document.querySelectorAll( '.code-tabs' );

	if ( ! tabLists.length ) {
		return;
	}

	tabLists.forEach( ( tabList ) => {
		const buttons = tabList.querySelectorAll( '.code-tab' );
		const panels = tabList.querySelectorAll( '.code-tab-block' );

		if ( ! buttons.length || ! panels.length || buttons.length !== panels.length ) {
			return;
		}

		tabList.setAttribute( 'role', 'tablist' );
		// Block button styles require a parent with `class*="wp-block"`.
		tabList.classList.add( 'wp-block', 'is-small', 'is-style-toggle' );

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

		const maybeActivateTab = ( currentButton ) => {
			if ( currentButton.classList.contains( 'is-active' ) ) {
				return;
			}

			const panelId = currentButton.getAttribute( 'aria-controls' );

			// Update button states.
			buttons.forEach( ( button ) => {
				button.classList.remove( 'is-active' );
				button.setAttribute( 'aria-selected', 'false' );
				button.setAttribute( 'tabindex', '-1' );
			} );
			currentButton.classList.add( 'is-active' );
			currentButton.setAttribute( 'aria-selected', 'true' );
			currentButton.setAttribute( 'tabindex', '0' );

			// Update panel states.
			panels.forEach( ( panel ) => {
				panel.classList.remove( 'is-active' );
			} );
			document.getElementById( panelId )?.classList.add( 'is-active' );
		};

		tabList.addEventListener( 'click', ( event ) => {
			if ( ! Array.from( buttons ).includes( event.target ) ) {
				return;
			}

			event.preventDefault();
			maybeActivateTab( event.target );
		} );

		tabList.addEventListener( 'keydown', ( event ) => {
			if ( ! Array.from( buttons ).includes( event.target ) ) {
				return;
			}

			if ( event.code === 'Enter' || event.code === 'Space' ) {
				event.preventDefault();
				maybeActivateTab( event.target );
			} else if ( event.code === 'ArrowLeft' || event.code === 'ArrowRight' ) {
				event.preventDefault();

				// Cycle focus within the tab list.
				const currentIndex = Array.from( buttons ).indexOf( event.target );
				const isMovingRight = event.code === 'ArrowRight';
				const nextIndex = isMovingRight ? currentIndex + 1 : currentIndex - 1;
				const nextButton = buttons[ nextIndex ];

				if ( nextButton ) {
					nextButton.focus();
				} else {
					buttons[ isMovingRight ? 0 : buttons.length - 1 ].focus();
				}
			}
		} );
	} );
};

window.addEventListener( 'load', init );
