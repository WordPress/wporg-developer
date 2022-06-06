<?php

namespace DevHub;

/**
 * Theme setup
 */
require __DIR__ . '/inc/php/theme.php';

/**
 * Theme blocks (should be moved to plugin in a future migration)
 */
require __DIR__ . '/inc/blocks/blocks.php';

// Require v1 theme functions for now until a future migration.
require dirname( __DIR__, 1 ) . '/wporg-developer/functions.php';
