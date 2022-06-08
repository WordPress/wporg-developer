<?php namespace DevHub; ?>

<?php if ( is_single() ) : 

	$content = get_reference_template_parts();

	// If the Handbook TOC is available, use it.
	if ( class_exists( 'WPorg_Handbook_TOC' ) ) :
		$TOC = new \WPorg_Handbook_TOC( get_parsed_post_types(), array(
			'header_text' => __( 'Contents', 'wporg' )
		) );
		
		$TOC_parts = $TOC->add_toc( $content );

		$content = $TOC_parts['content'];
		$toc = $TOC_parts['toc'];
	endif;

endif; ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php echo get_deprecated(); ?>

	<?php echo get_private_access_message(); ?>

	<header class="post-header">
		<div class="post-title">

			<h1><?php echo get_signature(); ?></h1>
			
			<section class="summary">
				<?php echo get_summary(); ?>
			</section>
		</div>
		<?php if ( is_single() ) :

			echo $toc;

		endif; ?>
	</header>

<?php if ( is_single() ) :  

	echo '<div class="content">' . $content . '</div>'; 

endif; ?>

</article>
