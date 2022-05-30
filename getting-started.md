# Getting Started

This document gives a few quick notes about how the codebase in this repo works and where to get started working on it.

The environment in this repo will set up a local WP install with the DevHub theme, and several other plugins and dependencies necessary for running a local test copy. Some of those pieces (such as the main wporg theme in `source/wp-content/themes/wporg`) are dependencies needed in order to run the environment but are not part of DevHub itself. The main parts of the codebase you are likely to need to change or refer to are outlined below.

## Structure

The main parts of the code are:

`source/wp-content/themes/wporg-developer` - this is the theme that is used to display doc pages. If you want to change front-end stuff, CSS, etc, then start here.

`source/wp-content/plugins/phpdoc-parser` - this is the code that imports PHPDoc documentiation blocks into the database.

`source/wp-content/themes/wporg-developer/inc/cli-commands.php` - a WP-CLI wrapper around the `phpdoc-parser` plugin that runs the PHPDoc import.

You can run an import in this repo with this command which is part of the setup:

    ```bash
    yarn parse
    ```

## Explanations

See `source/wp-content/themes/wporg-developer/inc/explanations.php` - a CPT where additional "Explanation" text can be stored for each function in the reference. The Explanation content is displayed under a `More Information` heading on the reference page for that function.


