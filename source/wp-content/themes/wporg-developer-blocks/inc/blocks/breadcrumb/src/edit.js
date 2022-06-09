import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	SelectControl,
	PanelBody,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit( { name, attributes, setAttributes } ) {
	function onChangeHandbook( newValue ) {
		setAttributes( { handbook: newValue } );
	}

	return (
		<p { ...useBlockProps() }>
			<ServerSideRender
				block={ name }
				attributes={ attributes }
			/>
		</p>
	);
}
