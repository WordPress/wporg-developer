/**
 * Table of contents JS.
 *
 * Stops the sticky table of contents on wide screens from overlapping the footer
 * by positioning it slightly above
 */

import { throttle } from './lodash.throttle.js';

document.body.onload = () => {
	const toc = document.querySelector( '.table-of-contents' );

	if ( ! toc ) {
		return;
	}

	const footer = document.querySelector( '.global-footer' );
	const tocStyleInitial = window.getComputedStyle( toc );
	const tocTop = parseInt( tocStyleInitial.top, 10 ) + parseInt( tocStyleInitial.marginTop, 10 );
	const tocTransformInitial = tocStyleInitial.transform;
	let tocHeight = toc.offsetHeight;

	const resetToCPosition = () => {
		if ( toc.style ) {
			toc.removeAttribute( 'style' );
		}
	};

	const setToCPosition = () => {
		if ( window.getComputedStyle( toc ).position !== 'fixed' ) {
			resetToCPosition();
			return;
		}

		const tocBottom = tocTop + tocHeight;
		const footerTopWithClearSpace = parseInt( footer.getBoundingClientRect().top, 10 ) - 30;

		// if the bottom of the toc is below the top of the footer's clear space move it above
		if ( tocBottom > footerTopWithClearSpace ) {
			toc.style.transform = `${ tocTransformInitial } translateY( ${
				footerTopWithClearSpace - tocBottom
			}px )`;
		} else {
			resetToCPosition();
		}
	};

	setToCPosition();

	window.addEventListener( 'scroll', throttle( setToCPosition, 25, { trailing: true } ) );
	window.addEventListener(
		'resize',
		throttle(
			() => {
				tocHeight = toc.offsetHeight;
				setToCPosition();
			},
			25,
			{ trailing: true }
		)
	);
};
