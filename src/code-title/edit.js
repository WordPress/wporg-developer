/**
 * WordPress dependencies
 */
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import DynamicEdit from '../shared/dynamic-edit';
import './edit.scss';

export default function Edit( { ...props } ) {
	const { attributes, setAttributes } = props;
	const { isLink, tagName } = attributes;
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'wporg' ) }>
					<ToggleControl
						label={ __( 'Make title a link', 'wporg' ) }
						checked={ isLink }
						onChange={ () => setAttributes( { isLink: ! isLink } ) }
					/>
				</PanelBody>
			</InspectorControls>
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
			<DynamicEdit { ...props } />
		</>
	);
}
