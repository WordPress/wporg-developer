<?php
/**
 * Reference Template: Source Information
 *
 * @package wporg-developer
 * @subpackage Reference
 */

namespace DevHub;

$source_file = get_source_file();
if ( ! empty( $source_file ) ) :
	?>
	<hr />
	<section class="source-content">
		<h2><?php _e( 'Source', 'wporg' ); ?></h2>
		<p>
			<?php printf( __( 'File: %s', 'wporg' ),
				'<a href="' . esc_url( get_source_file_archive_link( $source_file ) ) . '">' . esc_html( $source_file ) . '</a>'
			); ?>
		</p>

		<?php if ( post_type_has_source_code() ) : ?>
			<?php
				echo do_blocks(
					sprintf(
						'<!-- wp:code {"lineNumbers":true} --><pre class="wp-block-code"><code lang="php" class="language-php line-numbers">%s</code></pre><!-- /wp:code -->',
						htmlentities( get_source_code() )
					)
				);
			?>

			<p class="source-code-links">
				<span>
					<a href="#" class="show-complete-source"><?php _e( 'Expand full source code', 'wporg' ); ?></a>
					<a href="#" class="less-complete-source"><?php _e( 'Collapse full source code', 'wporg' ); ?></a>
				</span>
				<span><a href="<?php echo get_source_file_link(); ?>"><?php _e( 'View on Trac', 'wporg' ); ?></a></span>
				<span><a href="<?php echo get_github_source_file_link(); ?>"><?php _e( 'View on GitHub', 'wporg' ); ?></a></span>
			</p>
		<?php else : ?>
			<p>
				<a href="<?php echo get_source_file_link(); ?>"><?php _e( 'View on Trac', 'wporg' ); ?></a>
			</p>
		<?php endif; ?>
	</section>
<?php endif; ?>
