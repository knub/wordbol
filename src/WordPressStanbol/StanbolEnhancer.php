<?php

//namespace WordPressStanbol;

class StanbolEnhancer {

	function enhance($text) {
		$raw_enhancement = $this::request_enhancement($text);
		return $raw_enhancement;
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
			$resources = $graph->allOfType('http://fise.iks-project.eu/ontology/TextAnnotation');
			echo '<br /><br /><br /><br /><br /><ul>';
			echo '<pre>';
//			print_r($resources);
//			print_r($resources[2]->properties());
//			var_dump($resources[2]->getResource($resources[2]->properties()[0])->getUri());
			var_dump($resources[2]->getResource("rdf:type")->getUri());
//			var_dump($resources[2]->get('<http://fise.iks-project.eu/ontology/confidence>'));
//			var_dump($resources[0]->dump());
//			var_dump($resources[2]->get('http://purl.org/dc/terms/language'));
//			var_dump($resources[2]->get('http://purl.org/dc/terms/type'));
			echo '</pre>';
			echo '</ul>';
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