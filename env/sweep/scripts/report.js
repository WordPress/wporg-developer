/**
 * Merges the per-page sweep results in results/pages/ into results/report.json
 * and results/report.md. The JSON feeds the PHP Snippets Report admin page,
 * the markdown feeds the workflow job summary.
 *
 * Exits with a failure when the sweep produced no results, since that means
 * the pipeline itself is broken rather than a documentation example.
 */

/**
 * External dependencies
 */
const { existsSync, readdirSync, readFileSync, writeFileSync } = require( 'node:fs' );
const { join } = require( 'node:path' );

const RESULTS_DIR = join( __dirname, '..', 'results' );
const PAGES_DIR = join( RESULTS_DIR, 'pages' );

const BUCKET_LABELS = {
	'ran-match': 'Matched expected output',
	'ran-ok': 'Ran without expected output',
	'ran-mismatch': 'Differed from expected output',
	'ran-error': 'Failed to run',
	'no-output': 'Produced no output',
	'not-runnable': 'Rendered without a Run action',
	'never-completed': 'Never completed',
};

const BUCKETS = Object.keys( BUCKET_LABELS );

const FAILING_BUCKETS = [ 'ran-mismatch', 'ran-error', 'no-output', 'never-completed' ];

if ( ! existsSync( PAGES_DIR ) ) {
	console.error( 'No sweep results found in ' + PAGES_DIR + '. Run `yarn sweep` first.' );
	process.exit( 1 );
}

const results = readdirSync( PAGES_DIR )
	.filter( ( file ) => file.endsWith( '.json' ) )
	.sort()
	.flatMap( ( file ) => JSON.parse( readFileSync( join( PAGES_DIR, file ), 'utf8' ) ) );

if ( ! results.length ) {
	console.error( 'The sweep produced no results. The parse or import step is broken.' );
	process.exit( 1 );
}

const totals = Object.fromEntries( BUCKETS.map( ( bucket ) => [ bucket, 0 ] ) );
const pages = new Set();

for ( const result of results ) {
	totals[ result.bucket ]++;
	pages.add( result.type + '--' + result.page );
}

const failures = results.filter( ( result ) => FAILING_BUCKETS.includes( result.bucket ) );

writeFileSync(
	join( RESULTS_DIR, 'report.json' ),
	JSON.stringify( { generated: new Date().toISOString(), totals, results }, null, '\t' )
);

const markdown = [
	'# Snippet sweep report',
	'',
	results.length + ' snippets swept across ' + pages.size + ' pages.',
	'',
	'| Result | Count |',
	'| --- | ---: |',
	...BUCKETS.map( ( bucket ) => '| ' + BUCKET_LABELS[ bucket ] + ' | ' + totals[ bucket ] + ' |' ),
	'',
];

if ( failures.length ) {
	markdown.push(
		'## Snippets that need attention',
		'',
		'| Page | Snippet | Result | Status | Output |',
		'| --- | ---: | --- | --- | --- |',
		...failures.map(
			( failure ) =>
				'| [' + failure.page + '](' + failure.url + ') | ' +
				( failure.index + 1 ) + ' | ' +
				BUCKET_LABELS[ failure.bucket ] + ' | ' +
				failure.status + ' | ' +
				'`' + String( failure.output ).replace( /\n/g, ' ' ).slice( 0, 120 ) + '` |'
		),
		''
	);
}

writeFileSync( join( RESULTS_DIR, 'report.md' ), markdown.join( '\n' ) );

console.log( 'Wrote report.json and report.md to ' + RESULTS_DIR );
console.log( results.length + ' snippets: ' + BUCKETS.map( ( bucket ) => bucket + '=' + totals[ bucket ] ).join( ' ' ) );
