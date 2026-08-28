/**
 * Sweeps every reference page listed in results/manifest.json: runs each
 * php-snippet through WordPress Playground and records whether it completes
 * and matches its expected output. The manifest is written by manifest.php,
 * and report.js merges the per-page results into the sweep report.
 */

/**
 * External dependencies
 */
const { expect, test } = require( '@playwright/test' );
const { existsSync, mkdirSync, readFileSync, writeFileSync } = require( 'node:fs' );
const { join } = require( 'node:path' );

const RESULTS_DIR = join( __dirname, '..', 'results' );
const PAGES_DIR = join( RESULTS_DIR, 'pages' );
const MANIFEST = join( RESULTS_DIR, 'manifest.json' );

const INPUT_SELECTOR = 'script[type="application/x-php+json"]';
const EXPECTED_SELECTOR = 'script[type="text/expected-output+json"]';

// The first run on a page boots WordPress Playground in the browser, which
// downloads the PHP and WordPress WASM bundles from playground.wordpress.net.
const FIRST_RUN_TIMEOUT = 240000;
const RUN_TIMEOUT = 60000;

/**
 * Classifies one snippet run.
 *
 * @param {Object}      run        Run outcome.
 * @param {string}      run.status Why the run stopped.
 * @param {string}      run.output The output body text.
 * @param {string|null} expected   The declared expected output, if any.
 * @return {string} The bucket.
 */
function classify( run, expected ) {
	if ( 'not runnable' === run.status ) {
		return 'not-runnable';
	}

	if ( 'errored' === run.status ) {
		return 'ran-error';
	}

	if ( 'completed' !== run.status ) {
		return 'never-completed';
	}

	const output = run.output.trim();

	if ( null !== expected ) {
		return output === expected.trim() ? 'ran-match' : 'ran-mismatch';
	}

	if ( '' === output || '(no output)' === output ) {
		return 'no-output';
	}

	return 'ran-ok';
}

test.describe( 'classify', () => {
	test( 'not runnable', () => {
		expect( classify( { status: 'not runnable', output: '' }, null ) ).toBe( 'not-runnable' );
	} );

	test( 'never completed', () => {
		expect( classify( { status: 'run timeout', output: '' }, null ) ).toBe( 'never-completed' );
	} );

	test( 'runtime error', () => {
		expect( classify( { status: 'errored', output: 'Boot failed' }, '4' ) ).toBe( 'ran-error' );
	} );

	test( 'matches expected output', () => {
		expect( classify( { status: 'completed', output: '\n4\n' }, '4' ) ).toBe( 'ran-match' );
	} );

	test( 'differs from expected output', () => {
		expect( classify( { status: 'completed', output: '5' }, '4' ) ).toBe( 'ran-mismatch' );
	} );

	test( 'no expected output declared', () => {
		expect( classify( { status: 'completed', output: 'anything' }, null ) ).toBe( 'ran-ok' );
	} );

	test( 'empty output', () => {
		expect( classify( { status: 'completed', output: '(no output)' }, null ) ).toBe( 'no-output' );
	} );
} );

/**
 * Runs one php-snippet element and reports its outcome.
 *
 * The component's click handler sets aria-busy on the Run button
 * synchronously and removes it when the run settles, so the removal is the
 * completion signal.
 *
 * @param {import('@playwright/test').Locator} snippet The php-snippet locator.
 * @param {number}                             timeout Timeout in milliseconds.
 * @return {Promise<{status: string, output: string}>} The run outcome.
 */
async function runSnippet( snippet, timeout ) {
	try {
		return await snippet.evaluate(
			( element, milliseconds ) =>
				new Promise( ( resolve, reject ) => {
					const button = element.shadowRoot && element.shadowRoot.querySelector( '.run' );

					if ( ! button ) {
						reject( new Error( 'no run button' ) );

						return;
					}

					const timer = window.setTimeout( () => reject( new Error( 'run timeout' ) ), milliseconds );

					const observer = new MutationObserver( () => {
						if ( button.hasAttribute( 'aria-busy' ) ) {
							return;
						}

						window.clearTimeout( timer );
						observer.disconnect();

						const body = element.shadowRoot.querySelector( '.output-body' );

						resolve( {
							status: body && body.classList.contains( 'error' ) ? 'errored' : 'completed',
							output: body ? body.textContent : '',
						} );
					} );

					button.click();

					if ( ! button.hasAttribute( 'aria-busy' ) ) {
						window.clearTimeout( timer );
						reject( new Error( 'run did not start' ) );

						return;
					}

					observer.observe( button, { attributes: true, attributeFilter: [ 'aria-busy' ] } );
				} ),
			timeout
		);
	} catch ( error ) {
		return {
			status: String( error && error.message ? error.message : error ),
			output: '',
		};
	}
}

/**
 * Decodes the payload of a snippet script.
 *
 * The block encodes the payload as JSON, which keeps `<` escaped and the
 * payload inert in HTML. A payload that fails to decode is left to the run
 * itself, which reports the component's own error.
 *
 * @param {string} payload The script text.
 * @return {string|null} The payload, or null when it does not decode.
 */
function decodePayload( payload ) {
	try {
		const decoded = JSON.parse( payload );

		return 'string' === typeof decoded ? decoded : null;
	} catch {
		return null;
	}
}

test.describe( 'decodePayload', () => {
	test( 'json payload', () => {
		expect( decodePayload( '"string(5) \\"\\u003Cegg\\u003E\\""' ) ).toBe( 'string(5) "<egg>"' );
	} );

	test( 'json payload that is not a string', () => {
		expect( decodePayload( '{}' ) ).toBeNull();
	} );

	test( 'json payload that does not parse', () => {
		expect( decodePayload( 'string(4) "free"' ) ).toBeNull();
	} );
} );

/**
 * Reads the payload of the first snippet script matching a selector.
 *
 * @param {import('@playwright/test').Locator} snippet  The php-snippet locator.
 * @param {string}                             selector The script selector.
 * @return {Promise<string|null>} The payload, or null when the snippet declares none.
 */
async function readPayload( snippet, selector ) {
	const payload = await snippet.evaluate( ( element, query ) => {
		const found = element.querySelector( query );

		return found ? found.textContent || '' : null;
	}, selector );

	if ( null === payload ) {
		return null;
	}

	return decodePayload( payload );
}

if ( ! existsSync( MANIFEST ) ) {
	throw new Error( 'Missing ' + MANIFEST + '. Run `yarn sweep:prepare` first.' );
}

const manifest = JSON.parse( readFileSync( MANIFEST, 'utf8' ) );

mkdirSync( PAGES_DIR, { recursive: true } );

for ( const entry of manifest ) {
	test( `sweep ${ entry.type } ${ entry.slug }`, async ( { page } ) => {
		test.setTimeout( 20 * 60 * 1000 );

		const results = [];

		await page.goto( entry.url, { waitUntil: 'domcontentloaded' } );

		const snippets = page.locator( 'php-snippet' );

		await expect( snippets.first() ).toBeAttached( { timeout: 15000 } );

		// The php-snippet module is loaded from playground.wordpress.net, so the
		// elements upgrade after the page itself has rendered.
		await page.waitForFunction( () => undefined !== customElements.get( 'php-snippet' ), null, { timeout: 30000 } );

		const total = await snippets.count();

		let booted = false;

		for ( let index = 0; index < total; index++ ) {
			const snippet = snippets.nth( index );

			const input = await readPayload( snippet, INPUT_SELECTOR );
			const expected = await readPayload( snippet, EXPECTED_SELECTOR );
			const runnable = 'false' !== ( await snippet.getAttribute( 'runnable' ) );

			const run = runnable
				? await runSnippet( snippet, booted ? RUN_TIMEOUT : FIRST_RUN_TIMEOUT )
				: { status: 'not runnable', output: '' };

			booted = booted || runnable;

			results.push( {
				page: entry.slug,
				type: entry.type,
				url: entry.url,
				index,
				bucket: classify( run, expected ),
				status: run.status,
				input: null === input ? '' : input,
				output: run.output.trim(),
				expected: null === expected ? null : expected.trim(),
			} );
		}

		writeFileSync( join( PAGES_DIR, `${ entry.type }--${ entry.slug }.json` ), JSON.stringify( results, null, '\t' ) );
	} );
}
