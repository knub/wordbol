<?php

namespace WordPressStanbol;


class PostContentUpdater {

	private $content;
	private $offset = 0;

	function __construct($content) {
		$this->content = $content;
	}

	public function integrate_annotations($annotations) {
		$annotations->rewind();
		$keys = array();
		while ($annotations->valid()) {
			$key = $annotations->current();
			array_push($keys, $key);
			$annotations->next();
		}
		usort($keys, function($k1, $k2) {
			return $k1->get_start() - $k2->get_start();
		});

		foreach ($keys as $key) {
			$entity = $annotations[$key];
			$this->content = $this->integrate_annotation($key, $entity);
		}

		return $this->content;
	}

	function integrate_annotation($text, $entity) {
		$link = $entity[0]->get_resource();
		$end = $text->get_end();
//		if (strpos($content, '</a>', $end) < )
		$content = $this->content;
		$string_end = '</a>';
		$string_beginning = "<a href='$link'>";
		$content = substr_replace($content, $string_end, $text->get_end() + $this->offset, 0);
		$content = substr_replace($content, $string_beginning, $text->get_start() + $this->offset, 0);
		$this->offset += strlen($string_beginning) + strlen($string_end);
		return $content;
	}

} 