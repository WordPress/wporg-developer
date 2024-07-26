import * as IAPI from '@wordpress/interactivity';

const config = IAPI.getConfig( 'wporg/developer/dashicons-page' );
const { state } = IAPI.store( 'wporg/developer/dashicons-page', {
	state: {
		get iconClass() {
			return `dashicons ${ IAPI.getContext().icon }`;
		},

		get sectionAnchorTarget() {
			return `icons-${ IAPI.getContext().section.slug }`;
		},

		get sectionAnchorHref() {
			return `#icons-${ IAPI.getContext().section.slug }`;
		},

		get iconSectionLabel() {
			return IAPI.getContext().section.label;
		},

		get eachIcon() {
			return config.icons[ IAPI.getContext().icon ];
		},

		get style() {
			const cleanedValue = state.filter.replace( /[^a-zA-Z0-9-]/g, '' );
			return cleanedValue.length < 3
				? ''
				: `#iconlist li:not([data-keywords*="${ cleanedValue }" i]) { display: none; }`;
		},
	},

	handleIconClick: () => {
		const icon = IAPI.getContext().icon;
		state.selectedIcon = [ IAPI.getContext().icon ];
		const url = new URL( document.location.href );
		url.searchParams.set( 'icon', icon );
		window.history.replaceState( undefined, undefined, url );
		document.location.hash = '#glyph';
	},

	/** @param {InputEvent} event */
	handleIconFilter: ( event ) => {
		state.filter = event.target.value;
	},

	/**
	 * A previous implementation used #icon-slug to pick an ico.
	 * Respect those links, but replace the URL with our query-based version.
	 */
	init: () => {
		const url = new URL( document.location.href );
		const iconFromQuery = url.searchParams.get( 'icon' );

		// Prefer an icon from query, but if a hash (legacy link) includes it, fall-back to that.
		if ( ! Object.hasOwn( config.icons, iconFromQuery ) && url.hash ) {
			const iconFromHash = `dashicons-${ url.hash.substring( 1 ) }`;
			if ( Object.hasOwn( config.icons, iconFromHash ) ) {
				url.hash = '';
				state.selectedIcon = [ iconFromHash ];
				url.searchParams.set( 'icon', iconFromHash );
				window.history.replaceState( undefined, undefined, url );
			}
		}
	},

	copyClickHandlers: {
		css: makeCopyHandler( 'css' ),
		html: makeCopyHandler( 'html' ),
		glyph: makeCopyHandler( 'glyph' ),
	},
} );

/**
 * @param {'css'|'html'|'glyph'} type
 */
function makeCopyHandler( type ) {
	return () => {
		let text;
		let copyText;
		switch ( type ) {
			case 'css':
				text = config.texts.copyCss;
				copyText = `content: "\\${ state.eachIcon.code }"`;
				break;

			case 'html':
				text = config.texts.copyHtml;
				copyText = `<span class="dashicons ${ IAPI.getContext().icon }"></span>`;
				break;

			case 'glyph':
				text = config.texts.copyGlyph;
				const span = document.createElement( 'span' );
				span.innerHTML = `&#x${ state.eachIcon.code };`;
				copyText = span.textContent;
				break;
		}

		// eslint-disable-next-line no-alert
		window.prompt( text, copyText );
	};
}
