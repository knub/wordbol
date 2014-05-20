<?php

namespace WordPressStanbol\Models;

/**
 * A language enhancement declares the language of a given text.
 * @package WordPressStanbol\Models
 */
class LanguageEnhancement extends Enhancement {

	private $language;

	function __construct($language, $confidence) {
		parent::__construct($confidence);
		$this->set_language($language);
	}

	/**
	 * @return string The language for the given text as returned by Stanbol.
	 */
	public function get_language() {
		$this->get_language();
		return $this->language;
	}

	/**
	 * @param string $language The language to set for this enhancement.
	 */
	public function set_language($language) {
		$this->language = $language;
	}

} 