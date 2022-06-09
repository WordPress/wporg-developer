<?php namespace DevHub; ?>

<?php if ( is_single() ) : 

	$content = get_reference_template_parts();

	// If the Handbook TOC is available, use it.
	if ( class_exists( 'WPorg_Handbook_TOC' ) ) :
		$TOC = new \WPorg_Handbook_TOC( get_parsed_post_types(), array(
			'header_text' => __( 'Contents', 'wporg' )
		) );
		
		$parts = $TOC->parse_content( $content );

		$content = $parts['content'];
		$toc = $parts['toc'];
	endif;

endif; ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php echo get_deprecated(); ?>

	<?php echo get_private_access_message(); ?>

	<header class="post-header">
		<nav class="navigation">
			<?php if ( is_single() ) :

				echo function_reference_sidebar_menu();

			endif; ?>
		</nav>
		<div class="post-title">

			<h1><?php echo get_signature(); ?></h1>
			
			<section class="summary">
				<?php echo get_summary(); ?>
			</section>
		</div>
	</header>

<?php if ( is_single() ) :  

	echo '<div class="content">' . $content . '</div>'; 

endif; ?>

</article>
