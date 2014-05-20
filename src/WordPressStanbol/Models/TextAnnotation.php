<?php

namespace WordPressStanbol\Models;

class TextAnnotation {
	private $name;

	private $start;
	private $end;
	private $text;

	/**
	 * Constructs a new text-annotation.
	 * @param $name string The resource of the annotation, e.g. 'urn:enhancement-0df2ab8a-07d2-0da6-31e6-6a02857af3ca'
	 * @param $start int The start of the text annotation.
	 * @param $end int The end of the text annotation.
	 * @param $text string The text of the annotatoin.
	 */
	function __construct($name, $start, $end, $text) {
		$this->name = $name;
		$this->start = $start;
		$this->end   = $end;
		$this->text  = $text;
	}

	/**
	 * @return string The name of the resource, e.g. 'urn:enhancement-0df2ab8a-07d2-0da6-31e6-6a02857af3ca'
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @return int The start of the selection (as offset from the original text).
	 */
	public function get_start() {
		return $this->start;
	}

	/**
	 * @return int The end of the selection (as offset from the original text).
	 */
	public function get_end() {
		return $this->end;
	}

	/**
	 * @return string The selected text.
	 */
	public function get_text() {
		return $this->text;
	}
}