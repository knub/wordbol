<?php

namespace Wordbol\Models;

function compare_confidences($e1, $e2) {
	$v = $e2->get_confidence() - $e1->get_confidence();
	if ($v < 0)
		return -1;
	else if ($v > 0)
		return +1;
	return 0;
}
class EntityStorage extends \SplObjectStorage {
	public function getHash($text_annotation) {
		return $text_annotation->get_name();
	}
}
class EnhancementResult {

	// Stores the languages recognized in the text
	private $languages = array();
	// Saves information about resources, as a map from the resource string to an array containing information
	private $resource_info = array();
	private $entity_annotations;

	function __construct() {
		$this->entity_annotations = new EntityStorage();
	}

	/**
	 * @param $language_enhancement LanguageEnhancement The language enhancement to add.
	 */
	public function add_language($language_enhancement) {
		array_push($this->languages, $language_enhancement);
		usort($this->languages, array($this, 'compare_confidences'));
	}

	public function add_text_annotation($annotation) {
		if ($this->entity_annotations->contains($annotation))
			return;
		$this->entity_annotations[$annotation] = array();
	}

	public function add_entity_annotation_for($text_annotation_resource, $entity_annotation) {
		$text_annotation = new TextAnnotation($text_annotation_resource);
		if (!$this->entity_annotations->contains($text_annotation))
			throw new \Exception('Given TextAnnotation is unkown.');
		$entity_annotations = $this->entity_annotations[$text_annotation];
		array_push($entity_annotations, $entity_annotation);
		usort($entity_annotations, array($this, 'compare_confidences'));
		$this->entity_annotations[$text_annotation] = $entity_annotations;
	}

	public function add_resource_info($resource, $info) {
		$this->resource_info[$resource] = $info;
	}

	public function get_languages() {
		return $this->languages;

	}
	public function get_resource_info($resource) {
		return $this->resource_info[$resource];
	}

	public function get_entity_annotations() {
		return $this->entity_annotations;
	}

	public function compare_confidences($e1, $e2) {
		$v = $e2->get_confidence() - $e1->get_confidence();
		if ($v < 0)
			return -1;
		else if ($v > 0)
			return +1;
		return 0;
	}

}