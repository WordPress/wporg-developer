<?php

/**
 * Imports the handbook maintained alongside Reprint's client and server code.
 */
class DevHub_Reprint_Importer extends DevHub_Docs_Importer {
	/**
	 * Initializes object.
	 */
	public function init() {
		parent::do_init(
			'reprint',
			'reprint',
			'https://raw.githubusercontent.com/WordPress/reprint/trunk/docs/manifest.json'
		);
	}
}

DevHub_Reprint_Importer::instance()->init();
