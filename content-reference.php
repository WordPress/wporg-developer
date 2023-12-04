<?php namespace DevHub; ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php echo get_deprecated(); ?>

	<?php echo get_private_access_message(); ?>

	<h1><?php echo get_signature(); ?></h1>

	<section class="summary">
		<?php echo get_summary(); ?>
	</section>

<?php if ( is_single() ) : ?>

	<?php
	$content = get_reference_template_parts();

	// If the Handbook TOC is available, use it.
	if ( class_exists( 'WPorg_Handbook_TOC' ) ) :
		$TOC = new \WPorg_Handbook_TOC( get_parsed_post_types(), array(
			'header_text' => __( 'Contents', 'wporg' )
		) );

		$content = $TOC->add_toc( $content );

	endif;
	?>

	<?php echo $content; ?>

<?php endif; ?>

</article>
