# Getting Started

This document gives a few quick notes about how the codebase in this repo works and where to get started working on it.

The environment in this repo will set up a local WP install with the DevHub theme, and several other plugins and dependencies necessary for running a local test copy. Some of those pieces (such as the main wporg theme in `source/wp-content/themes/wporg`) are dependencies needed in order to run the environment but are not part of DevHub itself. The main parts of the codebase you are likely to need to change or refer to are outlined below.

## Structure

The main parts of the code are:

`source/wp-content/themes/wporg-developer` - this is the theme that is used to display doc pages. If you want to change front-end stuff, CSS, etc, then start here.

`source/wp-content/plugins/phpdoc-parser` - this is the [phpdoc-parser plugin](https://github.com/WordPress/phpdoc-parser) that imports PHPDoc documentiation blocks into the database.

`source/wp-content/themes/wporg-developer/inc/cli-commands.php` - a WP-CLI wrapper around the `phpdoc-parser` plugin. It handles things like fetching a fresh copy of WordPress to parse.

You can run an import in this repo with this command which is part of the setup:

    ```bash
    yarn parse
    ```

To import the Playground documentation from PHP, import the manifest first and
then import all Markdown files:

```php
do_action( 'devhub_playground_import_manifest' );

// Reprocess unchanged sources instead of skipping them based on their ETags.
add_filter( 'wporg_markdown_check_etags', '__return_false' );
do_action( 'devhub_playground_import_all_markdown' );
```

## Snippet sweep

The interactive PHP examples that phpdoc-parser exports from DocBlocks can be swept end to end: parse, import, render, and run every snippet through WordPress Playground, then report which examples run to completion.

```bash
yarn wp-env start
yarn sweep:prepare
yarn sweep
yarn sweep:report
```

`sweep:prepare` parses only the WordPress source files containing interactive snippet fences, so it is much faster than `yarn parse`.

The Tools → PHP Snippets Report admin page renders the report as bucket totals, a per-page rollup, and a filterable table where each snippet expands to show its input, its expected output, and the output it produced. The page reads `env/sweep/results/report.json` when a local sweep has written one, and otherwise reads the `report.json` that the weekly sweep workflow publishes to the `sweep-report` branch.

## Explanations

See `source/wp-content/themes/wporg-developer/inc/explanations.php` - a CPT where additional "Explanation" text can be stored for each function in the reference. The Explanation content is displayed under a `More Information` heading on the reference page for that function.

