import * as IAPI from '@wordpress/interactivity';

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
			return state.icons[ IAPI.getContext().icon ];
		},

		get selectedIconDetails() {
			return state.selectedIcon ? [ state.selectedIcon ] : [];
		},
	},
	actions: {
		handleIconClick() {
			state.selectedIcon = IAPI.getContext().icon;
		},
	},
} );
