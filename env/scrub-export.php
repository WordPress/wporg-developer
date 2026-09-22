<?php

/**
 * A CLI tool for scrubbing a WordPress WXR export file of likely PII.
 */

$opts = getopt( '', ['namespace:'], $last );

$namespace = $opts['namespace'] ?? 'http://wordpress.org/export/1.2/';
$infile = $argv[ $last ] ?? 'php://stdin';
$outfile = $argv[ $last + 1 ] ?? 'php://stdout';

if ( is_null( $infile ) ) {
	die( "Usage: {$argv[0]} [--namespace=http://wordpress.org/export/1.2/] <infile.wxr> [outfile.wxr]\n" );
}

$doc = new DomDocument();
if ( !$doc->load( $infile ) ) {
	fwrite( STDERR, "Unable to open $infile for writing.\n" );
	die(1);
}

$fp_out = fopen( $outfile, 'x' );
if ( !$fp_out ) {
	fwrite( STDERR, "Unable to open $outfile for writing.\n" );
	die(1);
}


fwrite( STDERR, "Scrubbing $infile to $outfile\n" );

// These are all in the `<wp:...>` namespace
$wp_elements_to_scrub = [
	'author_login',
	'author_email',
	'author_first_name',
	'author_last_name',
	'author_display_name',
	'comment_author',
	'comment_author_email',
	'comment_author_url',
	'comment_author_IP',
];

foreach ( $wp_elements_to_scrub as $tag ) {
	$count_replaced = 0;
	$nodes = $doc->getElementsByTagNameNS( $namespace, $tag );
	foreach( $nodes as $node ) {
		// There should only be one child (a text node) but let's loop just in case
		$done = 0;
		while ( $node->firstChild ) {
			if ( $node->removeChild( $node->firstChild ) ) {
				++ $done;
			}
		}
		if ( $done ) {
			$node->appendChild( new DOMText( '__REDACTED__' ) );
			++ $count_replaced;
		}
	}

	fwrite( STDERR, "Replaced $count_replaced instances of wp:$tag\n" );
}

fwrite( $fp_out, $doc->saveXML() );

fclose( $fp_out );