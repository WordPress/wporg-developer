document.querySelectorAll( '.playground-example-run' ).forEach( ( link ) => {
	link.addEventListener( 'click', ( event ) => {
		event.preventDefault();

		const iframe = document.createElement( 'iframe' );
		iframe.className = 'playground-example-iframe';
		iframe.title = 'WordPress Playground example';
		iframe.src = link.href;
		iframe.loading = 'lazy';
		iframe.style.width = '100%';
		iframe.style.height = '500px';
		iframe.style.border = '1px solid #ccc';

		link.closest( '.wp-block-button' ).replaceWith( iframe );
	} );
} );
