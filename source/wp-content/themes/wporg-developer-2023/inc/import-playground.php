<?php

class DevHub_Playground_Importer extends DevHub_Docs_Importer {
	const PHP_CODE_SNIPPET_SCRIPT_URL = 'https://playground.wordpress.net/php-code-snippet.js';
	const PLAYGROUND_DOCS_ASSET_URL   = 'https://wordpress.github.io/wordpress-playground/';
	const PLAYGROUND_IMAGE_META_KEY   = '_playground_image';

	/**
	 * The post currently being updated from Markdown.
	 *
	 * @var int
	 */
	protected $current_post_id = 0;

	/**
	 * Whether missing images may be downloaded in the current environment.
	 *
	 * @var bool
	 */
	protected $download_images = false;

	/**
	 * Initializes object.
	 */
	public function init() {
		parent::do_init(
			'playground',
			'playground',
			'https://raw.githubusercontent.com/WordPress/wordpress-playground/trunk/packages/docs/site/manifest.json'
		);

		add_filter( 'handbook_label', array( $this, 'change_handbook_label' ), 10, 2 );
		add_filter( 'wporg_markdown_before_transform', array( $this, 'transform_mdx' ), 10, 2 );
		add_filter( 'wporg_markdown_after_transform', array( $this, 'parse_callout_markdown' ), 10, 2 );
		add_filter( 'wporg_markdown_after_transform', array( $this, 'format_run_blueprint_links' ), 15, 2 );
		add_filter( 'wporg_markdown_after_transform', array( $this, 'rewrite_root_relative_links' ), 20, 2 );
		add_filter( 'wporg_markdown_after_transform', array( $this, 'rewrite_root_relative_image_sources' ), 20, 2 );
		add_filter( 'wporg_markdown_after_transform', array( $this, 'import_images' ), 25, 2 );
		add_filter( 'wporg_markdown_check_etags', array( $this, 'check_image_import_etag' ) );
		add_filter( 'script_loader_tag', array( $this, 'add_php_code_snippet_script_type' ), 10, 3 );
		add_filter( 'get_edit_post_link', array( $this, 'rewrite_markdown_edit_link' ), 11, 3 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_shortcode( 'playground_php_snippet', array( $this, 'render_php_code_snippet' ) );
	}

	/**
	 * Bypasses the ETag on production while a post has images to import.
	 *
	 * @param bool $check_etags Whether to check the stored ETag.
	 * @return bool
	 */
	public function check_image_import_etag( $check_etags ) {
		if ( ! $check_etags || ! $this->download_images || ! $this->current_post_id ) {
			return $check_etags;
		}

		$post = get_post( $this->current_post_id );
		if ( ! $post ) {
			return $check_etags;
		}

		foreach ( $this->get_external_image_urls( $post->post_content ) as $source_url ) {
			$image = $this->get_image_record( $post->ID, $source_url );
			if ( ! $image || ( 'downloaded' === $image['status'] && wp_get_attachment_url( $image['attachment_id'] ) ) ) {
				return false;
			}
		}

		return $check_etags;
	}

	/**
	 * Gets external standalone image URLs from content.
	 *
	 * @param string $html Post content.
	 * @return string[]
	 */
	protected function get_external_image_urls( $html ) {
		$attribute_sets = array();
		preg_match_all( '#<p\b[^>]*>\s*<img\b([^>]*)/?\s*>\s*</p>#i', $html, $paragraph_matches );
		preg_match_all( '#^[\t ]*<img\b([^>]*)/?\s*>[\t ]*$#im', $html, $line_matches );
		$attribute_sets = array_merge( $paragraph_matches[1], $line_matches[1] );
		$upload_url     = trailingslashit( wp_get_upload_dir()['baseurl'] );
		$source_urls    = array();

		foreach ( $attribute_sets as $attribute_string ) {
			$attributes = wp_kses_hair( $attribute_string, wp_allowed_protocols() );
			$source_url = isset( $attributes['src']['value'] ) ? html_entity_decode( $attributes['src']['value'], ENT_QUOTES | ENT_HTML5, 'UTF-8' ) : '';
			if ( ! wp_http_validate_url( $source_url ) || 0 === strpos( $source_url, $upload_url ) || attachment_url_to_postid( $source_url ) ) {
				continue;
			}

			$source_urls[] = esc_url_raw( $source_url );
		}

		return array_values( array_unique( $source_urls ) );
	}

	/**
	 * Makes the post ID available while Markdown filters reuse imported images.
	 *
	 * @param int $post_id Post ID to update.
	 * @return bool|WP_Error Whether the post was updated, or an error.
	 */
	protected function update_post_from_markdown_source( $post_id ) {
		$this->current_post_id = (int) $post_id;
		$this->download_images = 'production' === wp_get_environment_type();
		/**
		 * Filters whether Playground image import metadata should be cleared.
		 *
		 * @param bool $clear   Whether to clear image import records.
		 * @param int  $post_id Post ID being imported.
		 */
		if ( apply_filters( 'devhub_playground_clear_image_meta', false, $post_id ) ) {
			delete_post_meta( $post_id, self::PLAYGROUND_IMAGE_META_KEY );
		}

		try {
			return parent::update_post_from_markdown_source( $post_id );
		} finally {
			$this->current_post_id = 0;
			$this->download_images = false;
		}
	}

	/**
	 * Overrides the default handbook label.
	 *
	 * @param string $label     The default label.
	 * @param string $post_type The handbook post type.
	 * @return string
	 */
	public function change_handbook_label( $label, $post_type ) {
		if ( $this->get_post_type() === $post_type ) {
			$label = __( 'Playground Handbook', 'wporg' );
		}

		return $label;
	}

	/**
	 * Rewrites raw GitHub Markdown sources to their GitHub editor URLs.
	 *
	 * @param string $link    The post edit link.
	 * @param int    $post_id The post ID.
	 * @param string $context How to output the ampersand in the URL.
	 * @return string
	 */
	public function rewrite_markdown_edit_link( $link, $post_id, $context ) {
		if ( $this->get_post_type() !== get_post_type( $post_id ) ) {
			return $link;
		}

		return preg_replace(
			'!^https?://raw.githubusercontent.com/([^/]+/[^/]+)/(.*)$!i',
			'https://github.com/$1/edit/$2',
			$link
		);
	}

	/**
	 * Enqueues the interactive Blueprint example script.
	 */
	public function enqueue_scripts() {
		if ( ! is_singular( $this->get_post_type() ) ) {
			return;
		}

		$script_path = dirname( __DIR__ ) . '/js/playground-examples.js';

		wp_enqueue_script(
			'wporg-developer-playground-examples',
			get_stylesheet_directory_uri() . '/js/playground-examples.js',
			array(),
			filemtime( $script_path ),
			true
		);
	}

	/**
	 * Transforms supported MDX and removes its import declarations.
	 *
	 * @param string $markdown  The Markdown before it is transformed to HTML.
	 * @param string $post_type The post type being imported.
	 * @return string
	 */
	public function transform_mdx( $markdown, $post_type ) {
		if ( $this->get_post_type() !== $post_type ) {
			return $markdown;
		}

		$markdown = preg_replace( '/^\s*import\s+.+?\s+from\s+([\'\"]).+?\1;\s*$/m', '', $markdown );

		$markdown = preg_replace_callback(
			'/<BlueprintExample\b(.*?)\/\s*>/s',
			array( $this, 'transform_blueprint_example' ),
			preg_replace_callback(
				'/<PhpCodeSnippet(?:LiveExample|Example)\b[^>]*\/\s*>\s*(?=.*?```html[ \t]*\r?\n(.*?)\r?\n```)/s',
				array( $this, 'transform_php_code_snippet' ),
				$markdown
			)
		);

		return trim( $markdown );
	}

	/**
	 * Transforms a PHP snippet preview into a shortcode containing its adjacent embed.
	 *
	 * @param array $matches The matched component and HTML embed.
	 * @return string
	 */
	public function transform_php_code_snippet( $matches ) {
		return '[playground_php_snippet encoded="' . base64_encode( trim( $matches[1] ) ) . '"]' . "\n\n";
	}

	/**
	 * Renders a PHP snippet embed after post KSES has run.
	 *
	 * @param array $attributes Shortcode attributes.
	 * @return string
	 */
	public function render_php_code_snippet( $attributes ) {
		$attributes = shortcode_atts( array( 'encoded' => '' ), $attributes );
		$html       = base64_decode( $attributes['encoded'], true );

		if ( false === $html || false === strpos( $html, '<php-snippet' ) ) {
			return '';
		}

		wp_enqueue_script(
			'wporg-developer-php-code-snippet',
			self::PHP_CODE_SNIPPET_SCRIPT_URL,
			array(),
			null,
			true
		);

		return '<div class="php-code-snippet-live-example">' . $html . '</div>';
	}

	/**
	 * Loads the PHP snippet web component as an ES module.
	 *
	 * @param string $tag    The script tag.
	 * @param string $handle The script handle.
	 * @param string $src    The script source URL.
	 * @return string
	 */
	public function add_php_code_snippet_script_type( $tag, $handle, $src ) {
		if ( 'wporg-developer-php-code-snippet' !== $handle ) {
			return $tag;
		}

		return sprintf( '<script type="module" src="%s"></script>' . "\n", esc_url( $src ) );
	}

	/**
	 * Transforms a BlueprintExample MDX component into Markdown.
	 *
	 * @param array $matches The matched BlueprintExample component.
	 * @return string
	 */
	public function transform_blueprint_example( $matches ) {
		$attributes = $matches[1];
		$blueprint  = '';
		$display    = '';

		if ( preg_match( '/\bdisplay=\{`(.*?)`\s*\}/s', $attributes, $display_match ) ) {
			$display = trim( $display_match[1] );
		}

		if ( preg_match( '/\bblueprint=\{\{(.*)\}\}\s*$/s', $attributes, $blueprint_match ) ) {
			$blueprint = '{' . trim( $blueprint_match[1] ) . '}';
		}

		$shown_blueprint = $display ?: $blueprint;
		$parsed_blueprint = json_decode( $shown_blueprint, true );
		if ( JSON_ERROR_NONE !== json_last_error() ) {
			return $matches[0];
		}

		$output = array();
		if ( ! preg_match( '/\bjustButton(?:=\{true\})?/', $attributes ) ) {
			$output[] = "```json\n" . wp_json_encode( $parsed_blueprint, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n```";
		}

		if ( ! preg_match( '/\bnoButton(?:=\{true\})?/', $attributes ) ) {
			$encoded_blueprint = base64_encode( wp_json_encode( $parsed_blueprint, JSON_UNESCAPED_SLASHES ) );
			$playground_url = 'https://playground.wordpress.net/?mode=seamless#' . $encoded_blueprint;
			$output[] = '<div class="wp-block-button"><a class="wp-block-button__link wp-element-button playground-example-run" href="' . esc_url( $playground_url ) . '">Try it out!</a></div>';
		}

		return "\n\n" . implode( "\n\n", $output ) . "\n\n";
	}

	/**
	 * Parses inline Markdown inside Docusaurus callout HTML.
	 *
	 * Docusaurus supports Markdown inside callout HTML. Jetpack's Markdown
	 * parser leaves raw HTML block contents untouched, so run the inline parser
	 * over paragraph contents inside callout blocks.
	 *
	 * @param string $html      The transformed HTML.
	 * @param string $post_type The post type being imported.
	 * @return string
	 */
	public function parse_callout_markdown( $html, $post_type ) {
		if ( $this->get_post_type() !== $post_type || ! class_exists( 'WPCom_GHF_Markdown_Parser' ) ) {
			return $html;
		}

		$parser = new WPCom_GHF_Markdown_Parser();
		$parser->preserve_shortcodes = false;
		$parser->strip_paras = false;

		return preg_replace_callback(
			'#<div([^>]*class="[^"]*\bcallout\b[^"]*"[^>]*)>(.*?)</div>#is',
			function ( $callout ) use ( $parser ) {
				$content = trim( $callout[2] );

				if ( false !== strpos( $content, '<p>' ) ) {
					$content = preg_replace_callback(
						'#<p>(.*?)</p>#is',
						function ( $paragraph ) use ( $parser ) {
							return '<p>' . $parser->runSpanGamut( $paragraph[1] ) . '</p>';
						},
						$content
					);
				} else {
					$content = $parser->transform( $content );
				}
				$content = make_clickable( $content );

				return '<div' . $callout[1] . '>' . $content . '</div>';
			},
			$html
		);
	}

	/**
	 * Formats Blueprint action keyboard links as WordPress buttons.
	 *
	 * @param string $html      The transformed HTML.
	 * @param string $post_type The post type being imported.
	 * @return string
	 */
	public function format_run_blueprint_links( $html, $post_type ) {
		if ( $this->get_post_type() !== $post_type ) {
			return $html;
		}

		return preg_replace_callback(
			'#<a([^>]*)>\s*<kbd>(.*?)</kbd>\s*</a>#is',
			function ( $matches ) {
				$label = html_entity_decode( wp_strip_all_tags( $matches[2] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
				$label = trim( preg_replace( '/\s+/u', ' ', $label ) );

				if ( ! in_array( $label, array( 'Run Blueprint', 'See blueprint.json' ), true ) ) {
					return $matches[0];
				}

				$attributes = preg_replace( '/\sclass=(["\']).*?\1/i', '', $matches[1] );

				return '<span class="wp-block-button"><a' . $attributes . ' class="wp-block-button__link wp-element-button">' . esc_html( $label ) . '</a></span>';
			},
			$html
		);
	}

	/**
	 * Rewrites links rooted at the Playground docs site to the handbook base.
	 *
	 * The source documentation is built at the root of its own site and uses
	 * links such as `/blueprints`. On Developer Resources, the documentation is
	 * mounted below `/playground`, so those links need the handbook base added.
	 *
	 * @param string $html      The transformed HTML.
	 * @param string $post_type The post type being imported.
	 * @return string
	 */
	public function rewrite_root_relative_links( $html, $post_type ) {
		if ( $this->get_post_type() !== $post_type ) {
			return $html;
		}

		return preg_replace_callback(
			'#(<a\b[^>]*\bhref=["\'])/(?!/)([^"\']*)(["\'])#i',
			function ( $matches ) {
				return $matches[1] . trailingslashit( $this->get_base() ) . $matches[2] . $matches[3];
			},
			$html
		);
	}

	/**
	 * Rewrites images rooted at the Docusaurus site to its deployed asset URL.
	 *
	 * @param string $html      The transformed HTML.
	 * @param string $post_type The post type being imported.
	 * @return string
	 */
	public function rewrite_root_relative_image_sources( $html, $post_type ) {
		if ( $this->get_post_type() !== $post_type ) {
			return $html;
		}

		return preg_replace(
			'#(<img\b[^>]*\bsrc=["\'])/(?!/)#i',
			'$1' . self::PLAYGROUND_DOCS_ASSET_URL,
			$html
		);
	}

	/**
	 * Imports Playground documentation images and turns them into image blocks.
	 *
	 * @param string $html      The transformed HTML.
	 * @param string $post_type The post type being imported.
	 * @return string
	 */
	public function import_images( $html, $post_type ) {
		if ( $this->get_post_type() !== $post_type || ! $this->current_post_id ) {
			return $html;
		}

		$paragraph_pattern = '#<p\b[^>]*>\s*<img\b([^>]*)/?\s*>\s*</p>#i';
		$line_pattern      = '#^[\t ]*<img\b([^>]*)/?\s*>[\t ]*$#im';
		if ( class_exists( 'WP_CLI' ) ) {
			$total_images      = preg_match_all( '#<img\b#i', $html );
			$standalone_images = preg_match_all( $paragraph_pattern, $html ) + preg_match_all( $line_pattern, $html );
			WP_CLI::log(
				sprintf(
					'Post %d contains %d image(s); %d can be imported as standalone image blocks.',
					$this->current_post_id,
					$total_images,
					$standalone_images
				)
			);
		}

		$html = preg_replace_callback(
			$paragraph_pattern,
			array( $this, 'import_image' ),
			$html
		);

		$html = preg_replace_callback(
			$line_pattern,
			array( $this, 'import_image' ),
			$html
		);
		return $html;
	}

	/**
	 * Imports an image and returns a serialized core/image block.
	 *
	 * @param array $matches The matched image paragraph and its attributes.
	 * @return string
	 */
	protected function import_image( $matches ) {
		$attributes = wp_kses_hair( $matches[1], wp_allowed_protocols() );
		$source_url = isset( $attributes['src']['value'] ) ? html_entity_decode( $attributes['src']['value'], ENT_QUOTES | ENT_HTML5, 'UTF-8' ) : '';

		if ( ! wp_http_validate_url( $source_url ) ) {
			if ( class_exists( 'WP_CLI' ) ) {
				WP_CLI::warning( sprintf( 'Skipped image with invalid URL: %s', $source_url ? $source_url : '(empty)' ) );
			}

			return $matches[0];
		}

		$source_url = esc_url_raw( $source_url );

		$alt           = isset( $attributes['alt']['value'] ) ? $attributes['alt']['value'] : '';
		$image_record  = $this->get_image_record( $this->current_post_id, $source_url );
		$attachment_id = attachment_url_to_postid( $source_url );
		if ( ! $attachment_id && $image_record && 'downloaded' === $image_record['status'] ) {
			$attachment_id = absint( $image_record['attachment_id'] );
		}
		if ( ! $attachment_id ) {
			$attachment_id = $this->get_attachment_for_source( $source_url );
		}

		if ( $attachment_id ) {
			if ( class_exists( 'WP_CLI' ) ) {
				WP_CLI::log( sprintf( 'Reusing attachment %d for image %s.', $attachment_id, $source_url ) );
			}
		} else {
			if ( $image_record ) {
				if ( class_exists( 'WP_CLI' ) ) {
					WP_CLI::log( sprintf( 'Skipping previously processed image %s.', $source_url ) );
				}

				return $matches[0];
			}
			if ( ! $this->download_images ) {
				if ( class_exists( 'WP_CLI' ) ) {
					WP_CLI::log( sprintf( 'No imported attachment found for image %s; leaving it unchanged.', $source_url ) );
				}

				return $matches[0];
			}
			if ( class_exists( 'WP_CLI' ) ) {
				WP_CLI::log( sprintf( 'Downloading image %s.', $source_url ) );
			}

			if ( ! function_exists( 'media_sideload_image' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
				require_once ABSPATH . 'wp-admin/includes/media.php';
				require_once ABSPATH . 'wp-admin/includes/image.php';
			}

			$attachment_id = media_sideload_image( $source_url, $this->current_post_id, $alt, 'id' );
			if ( is_wp_error( $attachment_id ) ) {
				$this->set_image_record( $this->current_post_id, $source_url, 'failed' );
				if ( class_exists( 'WP_CLI' ) ) {
					WP_CLI::warning( sprintf( 'Could not import image %s: %s', $source_url, $attachment_id->get_error_message() ) );
				}

				return $matches[0];
			}

			if ( class_exists( 'WP_CLI' ) ) {
				WP_CLI::log( sprintf( 'Imported image %s as attachment %d.', $source_url, $attachment_id ) );
			}
		}

		$image_url = wp_get_attachment_image_url( $attachment_id, 'full' );
		if ( ! $image_url ) {
			if ( $this->download_images ) {
				$this->set_image_record( $this->current_post_id, $source_url, 'failed' );
			}
			return $matches[0];
		}
		if ( $this->download_images ) {
			$this->set_image_record( $this->current_post_id, $source_url, 'downloaded', $attachment_id );
		}

		$block_attributes = array(
			'id'              => $attachment_id,
			'sizeSlug'        => 'full',
			'linkDestination' => 'none',
		);
		$image_attributes = array(
			'src'   => $image_url,
			'alt'   => $alt,
			'class' => 'wp-image-' . $attachment_id,
		);

		return sprintf(
			"<!-- wp:image %s -->\n<figure class=\"wp-block-image size-full\"><img%s></figure>\n<!-- /wp:image -->",
			wp_json_encode( $block_attributes, JSON_UNESCAPED_SLASHES ),
			$this->serialize_html_attributes( $image_attributes )
		);
	}

	/**
	 * Gets a post's import record for an image source URL.
	 *
	 * @param int    $post_id    Post ID.
	 * @param string $source_url External image URL.
	 * @return array|null
	 */
	protected function get_image_record( $post_id, $source_url ) {
		foreach ( get_post_meta( $post_id, self::PLAYGROUND_IMAGE_META_KEY, false ) as $record ) {
			if ( is_array( $record ) && isset( $record['url'], $record['status'], $record['attachment_id'] ) && $source_url === $record['url'] ) {
				return $record;
			}
		}

		return null;
	}

	/**
	 * Stores the production import result for an image.
	 *
	 * @param int    $post_id       Post ID.
	 * @param string $source_url    External image URL.
	 * @param string $status        Either downloaded or failed.
	 * @param int    $attachment_id Attachment ID when downloaded.
	 */
	protected function set_image_record( $post_id, $source_url, $status, $attachment_id = 0 ) {
		$existing = $this->get_image_record( $post_id, $source_url );
		if ( $existing ) {
			delete_post_meta( $post_id, self::PLAYGROUND_IMAGE_META_KEY, $existing );
		}

		add_post_meta(
			$post_id,
			self::PLAYGROUND_IMAGE_META_KEY,
			array(
				'url'           => $source_url,
				'status'        => $status,
				'attachment_id' => (int) $attachment_id,
			)
		);
	}

	/**
	 * Finds an attachment previously imported from a source URL.
	 *
	 * @param string $source_url The remote image URL.
	 * @return int
	 */
	protected function get_attachment_for_source( $source_url ) {
		$attachments = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'fields'         => 'ids',
				'posts_per_page' => 1,
				'meta_key'       => '_source_url',
				'meta_value'     => esc_url_raw( $source_url ),
			)
		);

		return $attachments ? (int) $attachments[0] : 0;
	}

	/**
	 * Serializes HTML attributes with escaping.
	 *
	 * @param array $attributes Attribute names and values.
	 * @return string
	 */
	protected function serialize_html_attributes( $attributes ) {
		$html = '';
		foreach ( $attributes as $name => $value ) {
			$html .= sprintf( ' %s="%s"', esc_attr( $name ), esc_attr( $value ) );
		}

		return $html;
	}
}

DevHub_Playground_Importer::instance()->init();
