<?php

class DevHub_Playground_Importer extends DevHub_Docs_Importer {
	/**
	 * Initializes object.
	 */
	public function init() {
		$branch = defined( 'PLAYGROUND_BRANCH' ) ? PLAYGROUND_BRANCH : 'trunk';

		parent::do_init(
			'playground',
			'playground',
			"https://raw.githubusercontent.com/WordPress/wordpress-playground/{$branch}/packages/docs/site/manifest.json"
		);

		add_filter( 'handbook_label', array( $this, 'change_handbook_label' ), 10, 2 );
		add_filter( 'wporg_markdown_after_transform', array( $this, 'parse_callout_markdown' ), 10, 2 );
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
}

DevHub_Playground_Importer::instance()->init();
