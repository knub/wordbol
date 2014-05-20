<?php

namespace WordPressStanbol\Models;

/**
 * A language enhancement declares the language of a given text.
 * @package WordPressStanbol\Models
 */
class LanguageEnhancement extends Enhancement {

	private $language;

	/**
	 * @return string The language for the given text as returned by Stanbol.
	 */
	public function getLanguage() {
		$this->getLanguage();
		return $this->language;
	}

	/**
	 * @param string $language The language to set for this enhancement.
	 */
	public function setLanguage($language) {
		$this->language = $language;
	}

} 