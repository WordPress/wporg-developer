/**
 * External dependencies
 */
const { defineConfig } = require( '@playwright/test' );
const { join } = require( 'node:path' );

module.exports = defineConfig( {
	testDir: join( __dirname, 'tests' ),
	outputDir: join( __dirname, 'results', 'artifacts' ),
	timeout: 30 * 60 * 1000,
	fullyParallel: true,
	workers: 2,
	reporter: 'list',
	use: {
		actionTimeout: 30000,
		navigationTimeout: 60000,
	},
	projects: [
		{
			name: 'chromium',
			use: { browserName: 'chromium' },
		},
	],
} );
