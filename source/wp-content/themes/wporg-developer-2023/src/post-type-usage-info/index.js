/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import './style.scss';

function Edit() {
	return <div { ...useBlockProps() }>Post Type Usage Info</div>;
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
