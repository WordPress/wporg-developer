<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package wporg-developer
 */
?>

<?php if ( is_active_sidebar( get_post_type() ) ) : ?>
	<div id="secondary" class="widget-area" role="complementary">
		<a href="#" id="secondary-toggle"></a>
		<div id="secondary-content">
			<?php function_reference_sidebar_menu(); ?>
			<?php do_action( 'before_sidebar' ); ?>
			<?php dynamic_sidebar( get_query_var( 'current_handbook' ) ); ?>
		</div>
	</div><!-- #secondary -->
<?php endif; ?>
