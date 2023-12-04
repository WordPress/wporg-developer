<?php namespace DevHub;

/**
 * The template for displaying Archive pages.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package wporg-developer
 */

get_header(); ?>

	<div id="content-area" class="has-sidebar">

		<?php breadcrumb_trail(); ?>

		<main id="main" class="site-main" role="main">

			<?php if ( have_posts() ) : ?>

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>

					<?php
						get_template_part( 'content', 'handbook' );
					?>

				<?php endwhile; ?>

				<?php //wporg_developer_paging_nav(); ?>

			<?php else : ?>

				<?php get_template_part( 'content', 'none' ); ?>

			<?php endif; ?>
			<?php loop_pagination(); ?>
		</main>
		<!-- /wrapper -->
	<?php get_sidebar(); ?>
	</div><!-- /pagebody -->

<?php get_footer(); ?>
