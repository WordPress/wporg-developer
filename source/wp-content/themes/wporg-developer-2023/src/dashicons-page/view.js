import * as IAPI from '@wordpress/interactivity';

const config = IAPI.getConfig( 'wporg/dashicons-page' );
const { state } = IAPI.store( 'wporg/dashicons-page', {
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
			const cleanedValue = state.filter.replace( /[^a-zA-Z0-9]/, '' );
			return cleanedValue.length < 3
				? ''
				: `#iconlist li:not([data-keywords*="${ cleanedValue }" i]) { display: none; }`;
		},
	},

	handleIconClick: () => {
		state.selectedIcon = [ IAPI.getContext().icon ];
	},

	/** @param {InputEvent} event */
	handleIconFilter: ( event ) => {
		state.filter = event.target.value;
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
