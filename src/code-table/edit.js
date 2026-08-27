import ServerSideRender from '@wordpress/server-side-render';

export default function Edit( { name, attributes } ) {
	return <ServerSideRender block={ name } attributes={ attributes } />;
}
