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
	const labels = document.querySelectorAll( '.wp-block-wporg-handbook-pagination .post-navigation-link__label' );

	labels.forEach( ( label ) => {
		const visualLabel = label.innerHTML;
		const screenReaderText = label.classList.contains( 'post-navigation-link-previous' )
			? __( 'Previous chapter: ', 'wporg' )
			: __( 'Next chapter: ', 'wporg' );

		label.innerHTML = `<span aria-hidden="true">${ visualLabel }</span><span class="screen-reader-text">${ screenReaderText }</span>`;
	} );
};

window.addEventListener( 'load', init );
