/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Add screen reader text to handbook pagination links.
 * This is needed because the post-navigation-link block cannot accept rich content via the label attribute.
 *
 */
const init = () => {
	const links = document.querySelectorAll(
		'.wp-block-wporg-handbook-pagination .wp-block-post-navigation-link'
	);

	links.forEach( ( link ) => {
		const label = link.querySelector( '.post-navigation-link__label' );

		if ( label ) {
			const visualLabel = label.innerHTML;
			const screenReaderText = link.classList.contains( 'post-navigation-link-previous' )
				? __( 'Previous chapter: ', 'wporg' )
				: __( 'Next chapter: ', 'wporg' );

			label.innerHTML = `<span aria-hidden="true">${ visualLabel }</span><span class="screen-reader-text">${ screenReaderText }</span>`;
		}
	} );
};

window.addEventListener( 'load', init );
