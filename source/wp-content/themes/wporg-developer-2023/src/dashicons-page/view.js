import * as IAPI from '@wordpress/interactivity';

const { state } = IAPI.store( 'wporg/dashicons-page', {
	state: {
		get iconClass() {
			return `dashicons ${ IAPI.getContext().icon.slug }`;
		},

		get iconSectionSlug() {
			return IAPI.getContext().section.label.toLowerCase().replace( ' ', '-' );
		},

		get sectionAnchorTarget() {
			return `icons-${ state.iconSectionSlug }`;
		},

		get sectionAnchorHref() {
			return `#icons-${ state.iconSectionSlug }`;
		},
	},
	actions: {},
} );
