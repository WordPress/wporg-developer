import * as IAPI from '@wordpress/interactivity';

const { state } = IAPI.store( '@wporg-developer-2023/dashicons', {
	state: {
		get iconClass() {
			return `dashicons ${ IAPI.getContext().icon.slug }`;
		},
	},
	actions: {},
} );
