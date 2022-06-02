<?php
/**
 * Modified version of the embed template from wp-includes/embed-template.php
 *
 * @package wporg-developer
 */

use DevHub;

if ( ! headers_sent() ) {
	header( 'X-WP-embed: true' );
}

$post_id = get_the_ID();

$args                  = get_post_meta( $post_id, '_wp-parser_args', true );
$tags                  = get_post_meta( $post_id, '_wp-parser_tags', true );
$types                 = array();
$formatted_args        = array();
$PARAMETER_DISPLAY_MAX = 3;


if ( $tags ) {
	foreach ( $tags as $tag ) {
		if ( is_array( $tag ) && 'param' == $tag['name'] ) {
			$types[ $tag['variable'] ] = implode( ' | ', $tag['types'] );
		}
	}
}

// If it's a hook, the object is shaped differently
if ( 'wp-parser-hook' === get_post_type( $post_id ) ) {
	foreach ( $types as $arg => $type ) {
		$formatted_args[] = array( $arg, $type );
	}
} else {
	foreach ( $args as $arg ) {
		$name = '';

		if ( ! empty( $arg['name'] ) && ! empty( $types[ $arg['name'] ] ) ) {
			$name = $types[ $arg['name'] ];
		}

		if ( ! empty( $arg['name'] ) ) {
			$name = $arg['name'];
		}

		$formatted_args[] = array( $name, $types[ $arg['name'] ] );
	}
}


function get_signature( $post_id, $args ) {
	$title = get_the_title();

	if ( 'wp-parser-hook' === get_post_type( $post_id ) ) {
		$hook_type = DevHub\get_hook_type_name( $post_id );

		if ( $args ) {
			return $hook_type . '( "<span class="wp-embed-hook">' . $title . '</span>", ... )';
		}
		return $hook_type . '( "<span class="wp-embed-hook">' . $title . '</span>" )';
	}

	if ( $args ) {
		return $title . '( ... )';
	}

	return $title . '()';
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<title><?php echo esc_html( wp_get_document_title() ); ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<?php
	/**
	 * Print scripts or data in the embed template <head> tag.
	 *
	 * @since 4.4.0
	 */
	do_action( 'embed_head' );
	?>
	<style>
		code {
			background: #efefef;
			border-radius: 4px;
			padding: 2px 6px;
			font-weight: 400;
		}

		.wp-embed-heading {
			font-weight: normal;
		}

		.wp-embed-parameters-title {
			margin: 16px 0 8px;
			font-size: 14px;
			font-weight: normal;
		}

		.wp-embed-parameters ul {
			margin: 0 0 0 8px;
			padding: 0;
			list-style: none;
		}

		.wp-embed-parameters ul li {
			padding: 4px;
		}

		.wp-embed-parameters code {
			margin-right: 8px;
		}

		.wp-embed-hook {
			color: #24831d;
		}

	</style>
	
</head>
<body <?php body_class(); ?>>
	<div <?php post_class( 'wp-embed' ); ?>>

		<p class="wp-embed-heading">
			<a href="<?php the_permalink(); ?>" target="_top">
				<?php echo wp_kses_post( get_signature( $post_id, $formatted_args ) ); ?>
			</a>
		</p>

		<div class="wp-embed-excerpt">
			<?php the_excerpt(); ?>

			<?php if ( $formatted_args ) : ?>
				<div class="wp-embed-parameters">
					<h6 class="wp-embed-parameters-title">Parameters</h6>
					<ul>
						<?php for ( $i = 0; $i < min( count( $formatted_args ), $PARAMETER_DISPLAY_MAX ); $i++ ) : ?>
							<li>
								<code><?php echo esc_html( $formatted_args[ $i ][0] ); ?></code>
								<span class="wp-embed-parameters-type"><?php echo esc_html( $formatted_args[ $i ][1] ); ?></span>
							</li>
						<?php endfor; ?>

						<?php if ( count( $formatted_args ) > $PARAMETER_DISPLAY_MAX ) : ?>
							<li>... <?php echo count( $formatted_args ) - $PARAMETER_DISPLAY_MAX; ?> more </li>
						<?php endif; ?>
					</ul>
				</div>
			<?php endif; ?>

		</div>


		<?php
		/**
		 * Print additional content after the embed excerpt.
		 *
		 * @since 4.4.0
		 */
		do_action( 'embed_content' );
		?>

		<div class="wp-embed-footer">
			<?php the_embed_site_title(); ?>

			<div class="wp-embed-meta">
				<?php
				/**
				 * Prints additional meta content in the embed template.
				 *
				 * @since 4.4.0
				 */
				do_action( 'embed_content_meta' );
				?>
			</div>
		</div>
	</div>
</body>
</html>
