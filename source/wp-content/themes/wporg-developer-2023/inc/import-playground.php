<?php

class DevHub_Playground_Importer extends DevHub_Docs_Importer {
	const PHP_CODE_SNIPPET_SCRIPT_URL = 'https://playground.wordpress.net/php-code-snippet.js';

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
		add_filter( 'wporg_markdown_after_transform', array( $this, 'rewrite_root_relative_links' ), 20, 2 );
		add_filter( 'script_loader_tag', array( $this, 'add_php_code_snippet_script_type' ), 10, 3 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_shortcode( 'playground_php_snippet', array( $this, 'render_php_code_snippet' ) );
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

				return '<div' . $callout[1] . '>' . $content . '</div>';
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
}

DevHub_Playground_Importer::instance()->init();
