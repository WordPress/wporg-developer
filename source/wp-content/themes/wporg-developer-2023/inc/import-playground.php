<?php

class DevHub_Playground_Importer extends DevHub_Docs_Importer {
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

		return preg_replace_callback(
			'/<BlueprintExample\b(.*?)^\s*\/\s*>/ms',
			array( $this, 'transform_blueprint_example' ),
			$markdown
		);
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
			$output[] = '[Try it out!](https://playground.wordpress.net/?mode=seamless#' . $encoded_blueprint . ')';
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
