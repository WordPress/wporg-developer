<?php


 // WIP: very rough function reference menu. Lots of ugly code here, just PoC stuff.
 // If we're going forward with the idea then this needs to be rewritten to use something similar to WPorg_Handbook_Walker
 // (If it's even possible to use a page walker on CPTs like this)
 // Ideally this could be turned into a block.
 function function_reference_sidebar_menu() {
	?>
<aside id="handbook_pages-99" class="widget widget_wporg_handbook_pages">
	<h2 class="widget-title">Chapters</h2>
<div class="menu-table-of-contents-container">
<ul>
<li class="page_item menu-item page_item_has_children menu-item-has-children current_page_ancestor current-menu-ancestor open"><div class="expandable"><a href="https://developer.wordpress.org/reference/functions/">Functions</a><button class="dashicons dashicons-arrow-down-alt2" aria-expanded="false"></button></div>
<?php
	if ( 'wp-parser-function' === get_post_type() ) {
		global $post;

		$_post_id = $post->ID;

		$terms = wp_get_post_terms( $post->ID, 'wp-parser-package', [ 'fields' => 'names' ] );
		if ( $terms ) {
			// If more than one package, don't use WordPress.
			if ( count( $terms ) > 1 ) {
				$terms = array_diff( $terms, [ 'WordPress' ] );
			}

			$q1 = new WP_Query( [
				'post_type' => 'wp-parser-function',
				'posts_per_page' => -1,
				'orderby' => 'title',
				'order' => 'ASC',
				'tax_query' => [
						'relation' => 'OR',
						[
						'taxonomy' => 'wp-parser-package',
						'terms' => $terms,
						'field' => 'name',
					]
				]
			]);

			if ( $q1->found_posts ) {
				?>
		<ul class="children">
<?php
				while ( $q1->have_posts() ) {
					$q1->the_post();
					if ( get_the_ID() == $_post_id ) {
						?>
						<li class="page_item menu-item"><a class="active" href="<?php echo esc_url( get_the_permalink() ); ?>"><?php the_title() ?></a></li>
<?php
					} else {
						?>
						<li class="page_item menu-item"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php the_title() ?></a></li>
<?php
					}
				}
			}
			?>
			</ul>
	<?php

			wp_reset_postdata();
		}
	}
?>
</li>
<li class="page_item menu-item"><a href="https://developer.wordpress.org/reference/hooks/">Hooks</a></li>
<li class="page_item menu-item"><a href="https://developer.wordpress.org/reference/classes/">Classes</a></li>
<li class="page_item menu-item"><a href="https://developer.wordpress.org/reference/methods/">Methods</a></li>
 </ul>
 </div>
 </aside>
 <?php

 }