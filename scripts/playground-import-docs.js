#!/usr/bin/env node

const fs = require( 'fs' );
const path = require( 'path' );
const { spawnSync } = require( 'child_process' );

const root = path.resolve( __dirname, '..' );
const importerDir = path.join(
	root,
	'source/wp-content/themes/wporg-developer-2023/inc/local-importers'
);
const blueprintDir = path.join( root, 'source/wp-content/.playground' );
const baseBlueprintPath = path.join( root, 'source/wp-content/blueprint.json' );

function usage() {
	console.error( `Usage:
  yarn playground:import-docs --slug=<importer-slug> --manifest=<manifest-url> [--base=<url-base>] [--label=<label>] [--dry-run]

Example:
  yarn playground:import-docs --slug=playground --manifest=http://127.0.0.1:8765/manifest.json --label="Playground Handbook"

This generates a local importer and a matching combined boot/import blueprint,
then starts Playground with that blueprint.
` );
}

function parseArgs( args ) {
	const options = {};

	for ( let i = 0; i < args.length; i++ ) {
		const arg = args[ i ];

		if ( arg === '--dry-run' ) {
			options.dryRun = true;
			continue;
		}

		if ( ! arg.startsWith( '--' ) ) {
			throw new Error( `Unexpected argument: ${ arg }` );
		}

		const equalIndex = arg.indexOf( '=' );
		if ( equalIndex !== -1 ) {
			options[ arg.slice( 2, equalIndex ) ] = arg.slice( equalIndex + 1 );
			continue;
		}

		const key = arg.slice( 2 );
		const value = args[ i + 1 ];
		if ( ! value || value.startsWith( '--' ) ) {
			throw new Error( `Missing value for ${ arg }` );
		}

		options[ key ] = value;
		i++;
	}

	return options;
}

function sanitizeSlug( value ) {
	return value
		.toLowerCase()
		.trim()
		.replace( /[^a-z0-9_-]+/g, '-' )
		.replace( /^[-_]+|[-_]+$/g, '' );
}

function sanitizeBase( value ) {
	return value
		.toLowerCase()
		.trim()
		.replace( /[^a-z0-9_/-]+/g, '-' )
		.replace( /^\/+|\/+$/g, '' )
		.replace( /\/+/g, '/' );
}

function titleCase( value ) {
	return value
		.split( /[-_]+/ )
		.filter( Boolean )
		.map( ( part ) => part.charAt( 0 ).toUpperCase() + part.slice( 1 ) )
		.join( ' ' );
}

function classSuffix( value ) {
	return titleCase( value ).replace( /[^A-Za-z0-9]/g, '' );
}

function phpString( value ) {
	return `'${ value.replace( /\\/g, '\\\\' ).replace( /'/g, "\\'" ) }'`;
}

function buildImporter( { slug, base, manifest, label } ) {
	const className = `DevHub_Local_${ classSuffix( slug ) }_Importer`;

	return `<?php

class ${ className } extends DevHub_Docs_Importer {
\t/**
\t * Initializes object.
\t */
\tpublic function init() {
\t\tparent::do_init(
\t\t\t${ phpString( slug ) },
\t\t\t${ phpString( base ) },
\t\t\t${ phpString( manifest ) }
\t\t);

\t\tadd_filter( 'handbook_label', array( $this, 'change_handbook_label' ), 10, 2 );
\t\tadd_filter( 'wporg_markdown_after_transform', array( $this, 'parse_callout_markdown' ), 10, 2 );
\t}

\t/**
\t * Overrides the default handbook label.
\t *
\t * @param string $label     The default label.
\t * @param string $post_type The handbook post type.
\t * @return string
\t */
\tpublic function change_handbook_label( $label, $post_type ) {
\t\tif ( $this->get_post_type() === $post_type ) {
\t\t\t$label = __( ${ phpString( label ) }, 'wporg' );
\t\t}

\t\treturn $label;
\t}

\t/**
\t * Parses inline Markdown inside Docusaurus callout HTML.
\t *
\t * Docusaurus supports Markdown inside callout HTML. Jetpack's Markdown
\t * parser leaves raw HTML block contents untouched, so run the inline parser
\t * over paragraph contents inside callout blocks for this temporary importer.
\t *
\t * @param string $html      The transformed HTML.
\t * @param string $post_type The post type being imported.
\t * @return string
\t */
\tpublic function parse_callout_markdown( $html, $post_type ) {
\t\tif ( $this->get_post_type() !== $post_type || ! class_exists( 'WPCom_GHF_Markdown_Parser' ) ) {
\t\t\treturn $html;
\t\t}

\t\t$parser = new WPCom_GHF_Markdown_Parser();
\t\t$parser->preserve_shortcodes = false;
\t\t$parser->strip_paras = false;

\t\treturn preg_replace_callback(
\t\t\t'#<div([^>]*class="[^"]*\\bcallout\\b[^"]*"[^>]*)>(.*?)</div>#is',
\t\t\tfunction ( $callout ) use ( $parser ) {
\t\t\t\t$content = trim( $callout[2] );

\t\t\t\tif ( false !== strpos( $content, '<p>' ) ) {
\t\t\t\t\t$content = preg_replace_callback(
\t\t\t\t\t\t'#<p>(.*?)</p>#is',
\t\t\t\t\t\tfunction ( $paragraph ) use ( $parser ) {
\t\t\t\t\t\t\treturn '<p>' . $parser->runSpanGamut( $paragraph[1] ) . '</p>';
\t\t\t\t\t\t},
\t\t\t\t\t\t$content
\t\t\t\t\t);
\t\t\t\t} else {
\t\t\t\t\t$content = $parser->transform( $content );
\t\t\t\t}

\t\t\t\treturn '<div' . $callout[1] . '>' . $content . '</div>';
\t\t\t},
\t\t\t$html
\t\t);
\t}
}

${ className }::instance()->init();
`;
}

function buildBlueprint( { slug } ) {
	const baseBlueprint = JSON.parse( fs.readFileSync( baseBlueprintPath, 'utf8' ) );
	const manifestAction = `devhub_${ slug }_import_manifest`;
	const markdownAction = `devhub_${ slug }_import_all_markdown`;
	const code = `<?php
require_once '/wordpress/wp-load.php';

$actions = array(
\t'${ manifestAction }',
\t'${ markdownAction }',
);

foreach ( $actions as $action ) {
\tif ( ! has_action( $action ) ) {
\t\tthrow new RuntimeException( "The expected import action {$action} is not registered. Check the generated local importer." );
\t}

\tdo_action( $action );
}
`;

	return {
		...baseBlueprint,
		$schema: '../../../node_modules/@wp-playground/blueprints/blueprint-schema.json',
		steps: [
			...( baseBlueprint.steps || [] ),
			{
				step: 'runPHP',
				code,
			},
		],
	};
}

function main() {
	const options = parseArgs( process.argv.slice( 2 ) );

	if ( ! options.slug || ! options.manifest ) {
		usage();
		process.exitCode = 1;
		return;
	}

	const slug = sanitizeSlug( options.slug );
	if ( ! slug ) {
		throw new Error( 'The importer slug must contain at least one letter or number.' );
	}

	const config = {
		slug,
		base: options.base ? sanitizeBase( options.base ) : slug,
		manifest: options.manifest,
		label: options.label || `${ titleCase( slug ) } Handbook`,
	};

	fs.mkdirSync( importerDir, { recursive: true } );
	fs.mkdirSync( blueprintDir, { recursive: true } );

	const importerPath = path.join( importerDir, `import-${ slug }.php` );
	const blueprintPath = path.join( blueprintDir, `import-docs-${ slug }.blueprint.json` );

	fs.writeFileSync( importerPath, buildImporter( config ) );
	fs.writeFileSync( blueprintPath, `${ JSON.stringify( buildBlueprint( config ), null, '\t' ) }\n` );

	console.log( `Wrote ${ path.relative( root, importerPath ) }` );
	console.log( `Wrote ${ path.relative( root, blueprintPath ) }` );

	if ( options.dryRun ) {
		console.log( 'Dry run complete. Import was not executed.' );
		return;
	}

	const args = [
		'@wp-playground/cli',
		'start',
		'--php=8.4',
		'--no-auto-mount',
		'--mount=./source/wp-content/mu-plugins:/wordpress/wp-content/mu-plugins',
		'--mount=./env/0-sandbox.php:/wordpress/wp-content/mu-plugins/0-sandbox.php',
		'--mount=./source/wp-content/plugins:/wordpress/wp-content/plugins',
		'--mount=./source/wp-content/themes:/wordpress/wp-content/themes',
		`--blueprint=./${ path.relative( root, blueprintPath ) }`,
	];

	const result = spawnSync( 'npx', args, {
		cwd: root,
		stdio: 'inherit',
		shell: false,
	} );

	process.exitCode = result.status || 0;
}

try {
	main();
} catch ( error ) {
	console.error( error.message );
	usage();
	process.exitCode = 1;
}
