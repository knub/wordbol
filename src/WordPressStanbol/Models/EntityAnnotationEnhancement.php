<?php

namespace WordPressStanbol\Models;


class EntityAnnotationEnhancement extends Enhancement {

	private $resource;

	/**
	 * @return string The linked/annotated resource for this enhancement, e.g. 'http://dbpedia.org/resource/Paris'.
	 */
	public function getResource() {
		return $this->resource;
	}

	/**
	 * @param string $resource Sets the resource.
	 */
	public function setResource($resource) {
		$this->resource = $resource;
	}

} 