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
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'landing-links' ) }>
					<SelectControl
						label="Handbook"
						value={ attributes.handbook }
						options={ [
							{ value: 'not-selected', label: 'Select a Handbook' },
							{ value: 'blocks-handbook', label: 'Block Editor' },
							{ value: 'theme-handbook', label: 'Themes' },
							{ value: 'plugin-handbook', label: 'Plugins' },
							{ value: 'wpcs-handbook', label: 'Coding Standards' },
							{ value: 'rest-api-handbook', label: 'REST-API' },
							{ value: 'apis-handbook', label: 'Common APIs' },
							{ value: 'command', label: 'WP-CLI' },
						] }
						onChange={ onChangeHandbook }
					/>
				</PanelBody>
			</InspectorControls>
			<ServerSideRender
				block={ name }
				attributes={ attributes }
			/>
		</p>
	);
}
