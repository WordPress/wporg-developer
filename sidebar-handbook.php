<?php
/**
 * The sidebar template used in a handbook.
 *
 * @package wporg-developer
 */

if ( ! is_active_sidebar( wporg_get_current_handbook() ) )
	return;
?>

	<div id="secondary" class="widget-area" role="complementary">
		<a href="#" id="secondary-toggle"></a>
		<div id="secondary-content">
			<?php do_action( 'before_sidebar' ); ?>
			<?php dynamic_sidebar( get_query_var( 'current_handbook' ) ); ?>
		</div>
	</div><!-- #secondary -->

