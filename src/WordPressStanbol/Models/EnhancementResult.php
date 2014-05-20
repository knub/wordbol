<?php

namespace WordPressStanbol\Models;

class TextAnnotationStorage extends \SplObjectStorage {
	public function getHash($text_annotation) {
		return $text_annotation->get_name();
	}
}
class EnhancementResult {

	private $languages = array();
	private $entity_annotations;

	function __construct() {
		$this->entity_annotations = new TextAnnotationStorage();
	}

	/**
	 * @param $language_enhancement LanguageEnhancement The language enhancement to add.
	 */
	public function add_language($language_enhancement) {
		array_push($this->languages, $language_enhancement);
		usort($this->languages, function($le1, $le2) {
			$v = $le2->get_confidence() - $le1->get_confidence();
			if ($v < 0)
				return -1;
			else if ($v > 0)
				return +1;
			return 0;
		});
	}

	public function add_text_annotation($annotation) {
		if ($this->entity_annotations->contains($annotation))
			return;
		$this->entity_annotations[$annotation] = array();
	}

	public function add_entity_annotation($annotation) {

	}
}