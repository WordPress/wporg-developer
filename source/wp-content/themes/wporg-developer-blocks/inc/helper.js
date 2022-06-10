// const throttle = function throttle(fn, wait) {
//   var time = Date.now();
//   return function() {
//     if ((time + wait - Date.now()) < 0) {
//       fn();
//       time = Date.now();
//     }
//   }
// }

// document.addEventListener( 'DOMContentLoaded', function() {
// 	const headerHeight = parseInt( getComputedStyle(document.documentElement).getPropertyValue('--wp-global-header-height') );
// 	const titleContainerEl = document.querySelector( '.page-title-container' );
// 	const sidebarEl = document.querySelector( '.navigation-sidebar-scroll' );
// 	const isOutOfViewport = function (elem) {
// 		const bounding = elem.getBoundingClientRect();

// 		return bounding.bottom < headerHeight;
// 	}

// 	const doScroll = function() {
// 		if ( isOutOfViewport( titleContainerEl ) ) {
// 			const viewportHeight = window.innerHeight - headerHeight;
// 			sidebarEl.style.height = viewportHeight + 'px';
// 		} else {
// 			//sidebarEl.scrollTop = 0;
// 			sidebarEl.style.height = 'auto';
// 		}
// 	}
// 	//window.addEventListener('scroll', throttle( doScroll, 50 ), true );
// } );
