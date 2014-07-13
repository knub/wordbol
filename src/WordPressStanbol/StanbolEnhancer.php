<?php

namespace WordPressStanbol;

use EasyRdf_Graph;
use WordPressStanbol\Models\EnhancementResult;
use WordPressStanbol\Models\EntityAnnotation;
use WordPressStanbol\Models\EntityType;
use WordPressStanbol\Models\LanguageEnhancement;
use WordPressStanbol\Models\TextAnnotation;

/**
 * This class enhances a given text semantically using Apache Stanbol.
 */
class StanbolEnhancer {

	/**
	 * Enhances a given text with semantic annotations like language detection and entity annotation.
	 * @param $text string The text to enhance.
	 * @return EnhancementResult The enhancement result.
	 */
	function enhance($text) {
		$raw_enhancement = $this->request_enhancement($text);
		$enhancement_result = $this->build_enhancement_result($raw_enhancement);
		return $enhancement_result;
    }

	private function request_enhancement($text) {
		$parameters = $this->build_remote_post_parameters();
		$parameters['body'] = array('content' => $text);
		$response = wp_remote_post(STANBOL_INSTANCE, $parameters);

		return $this->handle_response($response);
	}

	private function build_enhancement_result($graph) {
		$enhancement_result = new EnhancementResult();
		$text_annotations = $graph->allOfType('http://fise.iks-project.eu/ontology/TextAnnotation');
		$this->build_language_result($enhancement_result, $text_annotations);
		$this->build_text_annotation_result($enhancement_result, $text_annotations);

		$enhancements = $graph->allOfType('http://fise.iks-project.eu/ontology/Enhancement');
		$this->build_entity_annotation_result($enhancement_result, $enhancements);

		$this->build_resource_info($enhancement_result, $graph);
		return $enhancement_result;
	}

	private function build_resource_info($enhancement_result, $graph) {
		$entity_annotations = $enhancement_result->get_entity_annotations();
		$entity_annotations->rewind();
		while ($entity_annotations->valid()) {
			$entities = $entity_annotations->getInfo();
			$entity_annotations->next();
			if (count($entities) === 0)
				continue;
			$entity = $entities[0];
			$resource = $entity->get_resource();
			// TODO/INFO: Taking only the first element so far. The first and second are almost always the same.
//			$depictions = $graph->all($resource, "<http://xmlns.com/foaf/0.1/depiction>");
			$depictions = array($graph->getResource($resource, "<http://xmlns.com/foaf/0.1/depiction>"));
			$depictions = array_map(function($depiction) { return $depiction->getUri(); }, $depictions);

			$comment = $graph->getLiteral($resource, "<http://www.w3.org/2000/01/rdf-schema#comment>")->getValue();
			$enhancement_result->add_resource_info($resource, array(
				"depictions" => $depictions,
				"comment" => $comment
			));
		}
	}

	private function build_language_result($enhancement_result, $text_annotations) {
		array_walk($text_annotations, function($annotation) use ($enhancement_result) {
			if (!$annotation->hasProperty('http://purl.org/dc/terms/language'))
				return;
			$language   = $annotation->getLiteral('<http://purl.org/dc/terms/language>')->getValue();
			$confidence = floatval($annotation->getLiteral('<http://fise.iks-project.eu/ontology/confidence>')->getValue());

			$enhancement_result->add_language(new LanguageEnhancement($language, $confidence));
		});
	}

	private function build_text_annotation_result($enhancement_result, $text_annotations) {
		array_walk($text_annotations, function($annotation) use ($enhancement_result) {
			if (!$annotation->hasProperty('http://fise.iks-project.eu/ontology/selected-text'))
				return;
			$name  = $annotation->getUri();
			$start = intval($annotation->getLiteral('<http://fise.iks-project.eu/ontology/start>')->getValue());
			$end   = intval($annotation->getLiteral('<http://fise.iks-project.eu/ontology/end>')->getValue());
			$text  = $annotation->getLiteral('<http://fise.iks-project.eu/ontology/selected-text>')->getValue();
			$confidence = floatval($annotation->getLiteral('<http://fise.iks-project.eu/ontology/confidence>')->getValue());
			$enhancement_result->add_text_annotation(new TextAnnotation($name, $confidence, $start, $end, $text));
		});
	}

	private function build_entity_annotation_result($enhancement_result, $entity_annotations) {
		array_walk($entity_annotations, function($annotation) use ($enhancement_result) {
			if (!$annotation->hasProperty('http://fise.iks-project.eu/ontology/entity-reference'))
				return;
			$text_annotations  = $annotation->allResources('<http://purl.org/dc/terms/relation>');
			array_walk($text_annotations, function($text_annotation) use ($annotation, $enhancement_result) {
				$entity_reference = $annotation->getResource('<http://fise.iks-project.eu/ontology/entity-reference>')->getUri();
				$type = $this->determine_entity_type($annotation);

				$confidence = floatval($annotation->getLiteral('<http://fise.iks-project.eu/ontology/confidence>')->getValue());
				$enhancement_result->add_entity_annotation_for($text_annotation->getUri(), new EntityAnnotation($entity_reference, $type, $confidence));
			});
		});

	}

	private function determine_entity_type($annotation) {
		$types = $annotation->allResources('<http://fise.iks-project.eu/ontology/entity-type>');

		foreach ($types as $type) {
			$uri = $type->getUri();
			if ($uri === 'http://dbpedia.org/ontology/Place') {
				return EntityType::Place;
			} else if ($uri === 'http://dbpedia.org/ontology/Person') {
				return EntityType::Person;
			} else if ($uri === 'http://schema.org/Organization') {
				return EntityType::Organization;
			}
		};
		return EntityType::Unknown;
	}

	private function build_remote_post_parameters() {
		return array(
			'httpversion' => '1.1',
			'headers' => array(
				'Accept' => 'text/rdf+nt',
				'Content-Type' => 'application/x-www-form-urlencoded',
			)
		);
	}

	private function handle_response($response) {
		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			throw new \Exception($error_message);
		} else {
			$graph = new EasyRdf_Graph();
			$graph->parse($response['body'], 'ntriples');
			return $graph;
		}
	}
}