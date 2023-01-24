/**
 * WordPress dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit( { name, attributes, children } ) {
	const blockProps = useBlockProps();
	return (
		<div { ...blockProps }>
			{ children }
			<ServerSideRender block={ name } attributes={ attributes } skipBlockSupportAttributes />
		</div>
	);
}
