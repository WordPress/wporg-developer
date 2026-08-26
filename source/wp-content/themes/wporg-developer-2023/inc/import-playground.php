<?php

class DevHub_Playground_Importer extends DevHub_Docs_Importer {
	const PHP_CODE_SNIPPET_SCRIPT_URL = 'https://playground.wordpress.net/php-code-snippet.js';
	const PLAYGROUND_DOCS_ASSET_URL   = 'https://wordpress.github.io/wordpress-playground/';
	const BLUEPRINT_STEPS_URL         = 'https://wordpress.github.io/wordpress-playground/blueprints/steps/';
	const PLAYGROUND_IMAGE_META_KEY   = '_playground_image';
	const CONTENT_TRANSFORM_META_KEY  = '_playground_content_transform_version';
	const CONTENT_TRANSFORM_VERSION   = 2;

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
		add_filter( 'wporg_markdown_check_etags', array( $this, 'check_content_transform_etag' ) );
		add_filter( 'wporg_markdown_check_etags', array( $this, 'check_blueprint_steps_import_etag' ) );
		add_filter( 'wporg_markdown_check_etags', array( $this, 'check_image_import_etag' ) );
		add_filter( 'script_loader_tag', array( $this, 'add_php_code_snippet_script_type' ), 10, 3 );
		add_filter( 'get_edit_post_link', array( $this, 'rewrite_markdown_edit_link' ), 11, 3 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_shortcode( 'playground_php_snippet', array( $this, 'render_php_code_snippet' ) );
	}

	/**
	 * Bypasses source ETags after the importer transformation logic changes.
	 *
	 * @param bool $check_etags Whether to check the stored ETag.
	 * @return bool
	 */
	public function check_content_transform_etag( $check_etags ) {
		if ( ! $check_etags || ! $this->current_post_id ) {
			return $check_etags;
		}

		$version = (int) get_post_meta( $this->current_post_id, self::CONTENT_TRANSFORM_META_KEY, true );
		if ( self::CONTENT_TRANSFORM_VERSION !== $version ) {
			return false;
		}

		return $check_etags;
	}

	/**
	 * Bypasses the source ETag for the generated Blueprint step reference.
	 *
	 * The rendered step reference can change when the TypeDoc model changes even
	 * when its Markdown source remains unchanged.
	 *
	 * @param bool $check_etags Whether to check the stored ETag.
	 * @return bool
	 */
	public function check_blueprint_steps_import_etag( $check_etags ) {
		if ( ! $check_etags || ! $this->current_post_id ) {
			return $check_etags;
		}

		$source_url = get_post_meta( $this->current_post_id, $this->meta_key, true );
		if ( preg_match( '#/docs/blueprints/05-steps\.md$#', $source_url ) ) {
			return false;
		}

		return $check_etags;
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
			$result = parent::update_post_from_markdown_source( $post_id );
			if ( true === $result ) {
				update_post_meta( $post_id, self::CONTENT_TRANSFORM_META_KEY, self::CONTENT_TRANSFORM_VERSION );
			}

			return $result;
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
			'#<UpdateTopLevelToc\b.*?/\s*>\s*<span>\s*\{BlueprintSteps\.map\(.*?</span>#s',
			array( $this, 'transform_blueprint_steps' ),
			$markdown
		);

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
	 * Replaces the dynamic Blueprint step reference with its rendered HTML.
	 *
	 * The source document builds this reference from TypeDoc data with React.
	 * The Markdown importer cannot execute that MDX, but the deployed Playground
	 * documentation contains the same sections rendered on the server.
	 *
	 * @param array $matches The dynamic Blueprint step MDX block.
	 * @return string
	 */
	public function transform_blueprint_steps( $matches ) {
		$response = wp_safe_remote_get( self::BLUEPRINT_STEPS_URL );
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return $matches[0];
		}

		$document        = new DOMDocument();
		$previous_errors = libxml_use_internal_errors( true );
		$loaded          = $document->loadHTML( wp_remote_retrieve_body( $response ) );
		libxml_clear_errors();
		libxml_use_internal_errors( $previous_errors );
		if ( ! $loaded ) {
			return $matches[0];
		}

		$xpath    = new DOMXPath( $document );
		$sections = $xpath->query( '//article//section[contains(concat(" ", normalize-space(@class), " "), " markdown ")]' );
		if ( ! $sections || 0 === $sections->length ) {
			return $matches[0];
		}

		$output = array();
		foreach ( $sections as $section ) {
			$code_blocks = $xpath->query( './/pre', $section );
			foreach ( $code_blocks as $pre ) {
				$lines = $xpath->query( './/*[contains(concat(" ", normalize-space(@class), " "), " token-line ")]', $pre );
				if ( ! $lines || 0 === $lines->length ) {
					continue;
				}

				$code_lines = array();
				foreach ( $lines as $line ) {
					$code_lines[] = $line->textContent;
				}

				$language = '';
				if ( preg_match( '/(?:^|\s)language-([a-z0-9_-]+)/i', $pre->getAttribute( 'class' ), $language_match ) ) {
					$language = $language_match[1];
				}

				while ( $pre->firstChild ) {
					$pre->removeChild( $pre->firstChild );
				}

				$code = $document->createElement( 'code' );
				if ( $language ) {
					$code->setAttribute( 'class', 'language-' . $language );
				}
				$code->appendChild( $document->createTextNode( implode( "\n", $code_lines ) ) );
				$pre->appendChild( $code );
			}

			$run_buttons = $xpath->query( './/button[normalize-space(.)="Try it out!"]', $section );
			foreach ( $run_buttons as $button ) {
				$example = $xpath->query( './/pre/code[contains(concat(" ", normalize-space(@class), " "), " language-json ")]', $section )->item( 0 );
				if ( ! $example ) {
					continue;
				}

				$blueprint = json_decode( $example->textContent, true );
				if ( JSON_ERROR_NONE !== json_last_error() ) {
					continue;
				}

				$playground_url = 'https://playground.wordpress.net/?mode=seamless#' . base64_encode( wp_json_encode( $blueprint, JSON_UNESCAPED_SLASHES ) );
				$wrapper        = $document->createElement( 'div' );
				$link           = $document->createElement( 'a', 'Try it out!' );
				$wrapper->setAttribute( 'class', 'wp-block-button' );
				$link->setAttribute( 'class', 'wp-block-button__link wp-element-button playground-example-run' );
				$link->setAttribute( 'href', esc_url( $playground_url ) );
				$wrapper->appendChild( $link );
				$button->parentNode->replaceChild( $wrapper, $button );
			}

			$output[] = $document->saveHTML( $section );
		}

		return "\n\n" . implode( "\n\n<hr>\n\n", $output ) . "\n\n";
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
		// This embed carries raw HTML, so only render it on its own imported handbook.
		if ( get_post_type() !== $this->get_post_type() ) {
			return '';
		}

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
	 * Some of those links point at a sibling doc using the path Docusaurus
	 * derives from its source filename (e.g. `/blueprints/tutorial/build-your-first-blueprint`,
	 * from `03-build-your-first-blueprint.md`), rather than the shorter `slug`
	 * the manifest assigns that doc (`build-your-first`). Such links are
	 * translated to the manifest slug path so they resolve.
	 *
	 * @param string $html      The transformed HTML.
	 * @param string $post_type The post type being imported.
	 * @return string
	 */
	public function rewrite_root_relative_links( $html, $post_type ) {
		if ( $this->get_post_type() !== $post_type ) {
			return $html;
		}

		$link_map = $this->get_manifest_link_map();

		return preg_replace_callback(
			'#(<a\b[^>]*\bhref=["\'])/(?!/)([^"\']*)(["\'])#i',
			function ( $matches ) use ( $link_map ) {
				$path        = $matches[2];
				$suffix_pos  = strcspn( $path, '?#' );
				$base_path   = rtrim( substr( $path, 0, $suffix_pos ), '/' );
				$suffix      = substr( $path, $suffix_pos );

				if ( isset( $link_map[ $base_path ] ) ) {
					$path = $link_map[ $base_path ] . $suffix;
				}

				return $matches[1] . trailingslashit( $this->get_base() ) . $path . $matches[3];
			},
			$html
		);
	}

	/**
	 * Builds a map of Docusaurus source-derived doc paths to their manifest slug paths.
	 *
	 * Docusaurus builds a doc's own link path from its filename, stripped of
	 * any leading numeric ordering prefix (e.g. `01-`) and extension, while
	 * keeping its directory structure. The manifest instead assigns each doc
	 * its own explicit path via `slug`/`parent`, which can differ from that
	 * derived path. Cross-links within the docs that use the source-derived
	 * path therefore need translating to the manifest path to resolve once
	 * imported.
	 *
	 * @return array Map of source-derived path to manifest key, for docs
	 *               where the two differ.
	 */
	protected function get_manifest_link_map() {
		$transient_key = 'devhub_playground_manifest_link_map';
		$map           = get_transient( $transient_key );
		if ( is_array( $map ) ) {
			return $map;
		}

		$map      = array();
		$response = wp_remote_get( $this->get_manifest_url() );

		if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
			$manifest = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( is_array( $manifest ) ) {
				foreach ( $manifest as $key => $doc ) {
					if ( ! is_string( $key ) || empty( $doc['markdown_source'] ) ) {
						continue;
					}

					$source_path = $this->derive_source_link_path( $doc['markdown_source'] );
					if ( $source_path && $source_path !== $key ) {
						$map[ $source_path ] = $key;
					}
				}
			}
		}

		set_transient( $transient_key, $map, 15 * MINUTE_IN_SECONDS );

		return $map;
	}

	/**
	 * Derives the link path Docusaurus would generate for a doc's own source file.
	 *
	 * @param string $markdown_source The markdown_source value from the manifest.
	 * @return string
	 */
	protected function derive_source_link_path( $markdown_source ) {
		$path = preg_replace( '#^docs/#', '', $markdown_source );
		$path = preg_replace( '#\.mdx?$#', '', $path );

		$segments = array_map(
			function ( $segment ) {
				return preg_replace( '/^\d+-/', '', $segment );
			},
			explode( '/', $path )
		);

		return implode( '/', $segments );
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

		$alt           = isset( $attributes['alt']['value'] ) ? html_entity_decode( $attributes['alt']['value'], ENT_QUOTES | ENT_HTML5, 'UTF-8' ) : '';
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
