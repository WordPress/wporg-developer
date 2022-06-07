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

// Require current active theme functions for now so all the improvements are inherited.
require dirname( __DIR__, 1 ) . '/wporg-developer/functions.php';
