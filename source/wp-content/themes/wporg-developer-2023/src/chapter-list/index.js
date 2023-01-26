/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit: ( { name, attributes } ) => {
		return <ServerSideRender block={ name } attributes={ attributes } skipBlockSupportAttributes />;
	},
} );
