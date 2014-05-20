<?php

namespace WordPressStanbol\Models;


class EntityAnnotationEnhancement extends Enhancement {

	private $resource;

	function __construct($resource) {
		$this->resource = $resource;
	}

	/**
	 * @return string The linked/annotated resource for this enhancement, e.g. 'http://dbpedia.org/resource/Paris'.
	 */
	public function getResource() {
		return $this->resource;
	}

}