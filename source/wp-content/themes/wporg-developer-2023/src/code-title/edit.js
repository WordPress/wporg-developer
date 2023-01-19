/**
 * Wordpress dependencies
 */
import { InspectorControls } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function Edit( { attributes, setAttributes, name } ) {
	const { tagName } = attributes;
	return (
		<>
			<InspectorControls __experimentalGroup="advanced">
				<SelectControl
					label={ __( 'HTML element', 'wporg' ) }
					options={ [
						{ label: __( 'Default (<p>)', 'wporg' ), value: 'p' },
						{ label: '<div>', value: 'div' },
						{ label: '<h1>', value: 'h1' },
						{ label: '<h2>', value: 'h2' },
						{ label: '<h3>', value: 'h3' },
						{ label: '<h4>', value: 'h4' },
						{ label: '<h5>', value: 'h5' },
						{ label: '<h6>', value: 'h6' },
					] }
					value={ tagName }
					onChange={ ( val ) => setAttributes( { tagName: val } ) }
				/>
			</InspectorControls>
			<ServerSideRender block={ name } attributes={ attributes } />
		</>
	);
}
