<?php
/**
 * Title: No Search Results
 * Slug: wporg-developer-2023/no-search-results
 * Inserter: no
 */

?>

<!-- wp:paragraph {"placeholder":"Add text or blocks that will display when a query returns no results.","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--40)"><?php esc_attr_e( 'Sorry, but nothing matched your search terms.', 'wporg' ); ?></p>
<!-- /wp:paragraph -->
