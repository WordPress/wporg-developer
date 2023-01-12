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
	return <div { ...useBlockProps() }>Search Title</div>;
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
