<?php

namespace WordPressStanbol;

use EasyRdf_Graph;
use WordPressStanbol\Models\EnhancementResult;
use WordPressStanbol\Models\LanguageEnhancement;

class StanbolEnhancer {

	function enhance($text) {
		$raw_enhancement = $this::request_enhancement($text);
		$enhancement_result = $this->build_enhancement_result($raw_enhancement);
		return $enhancement_result;
    }

	private function build_enhancement_result($graph) {
		$enhancement_result = new EnhancementResult();
		$text_annotations = $graph->allOfType('http://fise.iks-project.eu/ontology/TextAnnotation');
		array_walk($text_annotations, function($annotation) use ($enhancement_result) {
			if (!$annotation->hasProperty('http://purl.org/dc/terms/language'))
				return;
			$language = $annotation->getLiteral('<http://purl.org/dc/terms/language>')->getValue();
			$confidence = floatval($annotation->getLiteral('<http://fise.iks-project.eu/ontology/confidence>')->getValue());
			$enhancement_result->add_language(new LanguageEnhancement($language, $confidence));
		});
		return $enhancement_result;
	}
	private function request_enhancement($text) {
		$parameters = $this::build_remote_post_parameters();
		$parameters['body'] = array('content' => $text);
		$response = wp_remote_post(STANBOL_INSTANCE, $parameters);

		return $this::handle_response($response);
	}

	private function handle_response($response) {
		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			throw new Exception($error_message);
		} else {
			$graph = new EasyRdf_Graph();
			$graph->parse($response['body'], 'turtle');
			return $graph;
		}
	}

	private function build_remote_post_parameters() {
		return array(
			'httpversion' => '1.1',
			'headers' => array(
				'Accept' => 'text/turtle',
				'Content-Type' => 'application/x-www-form-urlencoded',
			)
		);
	}
}