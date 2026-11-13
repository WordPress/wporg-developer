const init = () => {
	const report = document.querySelector( '.devhub-snippets-report' );

	if ( ! report ) {
		return;
	}

	const filters = {
		bucket: report.querySelector( '[data-filter="bucket"]' ),
		page: report.querySelector( '[data-filter="page"]' ),
		search: report.querySelector( '[data-filter="search"]' ),
	};

	if ( ! filters.bucket || ! filters.page || ! filters.search ) {
		return;
	}

	const cards = report.querySelectorAll( '.snippets-report-card' );
	const rows = report.querySelectorAll( '.snippets-report-row' );
	const shown = report.querySelector( '.snippets-report-shown' );
	const empty = report.querySelector( '.snippets-report-empty' );
	const anchor = report.querySelector( '#snippets-report-snippets' );

	const matches = ( row, bucket, page, search ) => {
		if ( 'all' !== bucket && row.dataset.bucket !== bucket ) {
			return false;
		}

		if ( 'all' !== page && row.dataset.page !== page ) {
			return false;
		}

		return '' === search || row.dataset.search.includes( search );
	};

	const apply = () => {
		const bucket = filters.bucket.value;
		const page = filters.page.value;
		const search = filters.search.value.trim().toLowerCase();

		let count = 0;

		rows.forEach( ( row ) => {
			const visible = matches( row, bucket, page, search );
			const toggle = row.querySelector( '[aria-expanded]' );

			row.hidden = ! visible;
			row.nextElementSibling.hidden = ! visible || 'true' !== toggle.getAttribute( 'aria-expanded' );

			if ( visible ) {
				count++;
			}
		} );

		cards.forEach( ( card ) => {
			const selected = card.dataset.bucket === bucket;

			card.classList.toggle( 'is-selected', selected );
			card.setAttribute( 'aria-pressed', selected ? 'true' : 'false' );
		} );

		shown.textContent = count.toLocaleString();
		empty.hidden = 0 !== count;
	};

	const sort = ( header ) => {
		const headers = Array.from( header.parentElement.children );
		const body = header.closest( 'table' ).tBodies[ 0 ];
		const index = headers.indexOf( header );
		const ascending = 'ascending' !== header.getAttribute( 'aria-sort' );
		const numeric = 'number' === header.dataset.sort;

		headers.forEach( ( each ) => each.removeAttribute( 'aria-sort' ) );
		header.setAttribute( 'aria-sort', ascending ? 'ascending' : 'descending' );

		Array.from( body.rows )
			.sort( ( one, two ) => {
				const first = one.cells[ index ].textContent.trim();
				const second = two.cells[ index ].textContent.trim();
				const order = numeric ? Number( first ) - Number( second ) : first.localeCompare( second );

				return ascending ? order : -order;
			} )
			.forEach( ( row ) => body.appendChild( row ) );
	};

	Object.values( filters ).forEach( ( filter ) => {
		filter.addEventListener( 'input', apply );
	} );

	cards.forEach( ( card ) => {
		card.addEventListener( 'click', () => {
			filters.bucket.value = card.dataset.bucket;
			apply();
		} );
	} );

	report.querySelectorAll( '.snippets-report-page' ).forEach( ( button ) => {
		button.addEventListener( 'click', () => {
			filters.page.value = button.dataset.page;
			apply();
			anchor.scrollIntoView( { behavior: 'smooth', block: 'start' } );
		} );
	} );

	report.querySelectorAll( '.snippets-report-sortable' ).forEach( ( header ) => {
		header.addEventListener( 'click', () => sort( header ) );
	} );

	rows.forEach( ( row ) => {
		const toggle = row.querySelector( '[aria-expanded]' );

		row.addEventListener( 'click', ( event ) => {
			if ( event.target.closest( 'a' ) ) {
				return;
			}

			const expanded = 'true' === toggle.getAttribute( 'aria-expanded' );

			toggle.setAttribute( 'aria-expanded', expanded ? 'false' : 'true' );
			row.classList.toggle( 'is-expanded', ! expanded );
			row.nextElementSibling.hidden = expanded;
		} );
	} );

	apply();
};

window.addEventListener( 'load', init );
