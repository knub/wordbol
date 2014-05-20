<?php

namespace WordPressStanbol\Models;


class EnhancementResult {

	private $languages = array();

	/**
	 * @param $language_enhancement LanguageEnhancement The language enhancement to add.
	 */
	public function add_language($language_enhancement) {
		array_push($this->languages, $language_enhancement);
		usort($this->languages, function($le1, $le2) {
			return $le1->get_confidence() - $le2->get_confidence();
		});
	}

} 