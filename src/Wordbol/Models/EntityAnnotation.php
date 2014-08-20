<?php

namespace Wordbol\Models;


class EntityAnnotation extends Enhancement {

	private $resource;
	private $entity_type;

	function __construct($resource, $entity_type, $confidence) {
		parent::__construct($confidence);
		$this->resource = $resource;
		$this->entity_type = $entity_type;
	}

	/**
	 * @return string The linked/annotated resource for this enhancement, e.g. 'http://dbpedia.org/resource/Paris'.
	 */
	public function get_resource() {
		return $this->resource;
	}

	/**
	 * @return Wordbol\Models\EntityType The type of this resource, e.g. EntityType::Person.
	 */
	public function get_entity_type() {
		return $this->entity_type;
	}

}