import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit( { name, attributes } ) {
	return (
		<p { ...useBlockProps() }>
			<ServerSideRender block={ name } attributes={ attributes } />
		</p>
	);
}
