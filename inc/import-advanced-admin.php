<?php

class DevHub_Advanced_Admin extends DevHub_Docs_Importer {
	/**
	 * Initializes object.
	 */
	public function init() {
		parent::do_init(
			'adv-admin', // 'advanced-administration' makes for too long of a post type slug when appended with '-handbook'
			'advanced-administration',
			'https://raw.githubusercontent.com/WordPress/Advanced-administration-handbook/main/bin/handbook-manifest.json'
		);

		add_filter( 'handbook_label', array( $this, 'change_handbook_label' ), 10, 2 );
	}

	/**
	 * Overrides the default handbook label since post type name does not directly
	 * translate to post type label.
	 *
	 * @param string $label     The default label, which is merely a sanitized
	 *                          version of the handbook name.
	 * @param string $post_type The handbook post type.
	 * @return string
	 */
	public function change_handbook_label( $label, $post_type ) {
		if ( $this->get_post_type() === $post_type ) {
			$label = __( 'Advanced Administration Handbook', 'wporg' );
		}

		return $label;
	}
}

DevHub_Advanced_Admin::instance()->init();
