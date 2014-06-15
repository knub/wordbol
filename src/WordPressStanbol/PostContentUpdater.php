<?php

namespace WordPressStanbol;


class PostContentUpdater {

	private $content;
	private $offset = 0;

	function __construct($content) {
		$this->content = $content;
	}

	/**
	 * Integrates entity annotations in the contenet via links.
	 * @param $annotations array The entity annotations to integrate.
	 * @return string The modified content
	 */
	public function integrate_annotations($annotations) {
		// first, we need to sort the keys array, because we have to insert the links in ascending order to make
		// calculating the offset easier.
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
		// get best fitting resource
		if (count($entity) === 0)
			return $this->content;
		$link = $entity[0]->get_resource();
		$end = $text->get_end();
		$content = $this->content;
		if ($this->already_linked($content, $end))
			return $content;
		$string_end = '</a>';
		$string_beginning = "<a href='$link'>";
		$content = substr_replace($content, $string_end, $end + $this->offset, 0);
		$content = substr_replace($content, $string_beginning, $text->get_start() + $this->offset, 0);
		$this->offset += strlen($string_beginning) + strlen($string_end);
		return $content;
	}

	/**
	 * Checks whether the found entity is already linked. It does so by checking what comes first
	 * after the entity text: a closing link '</a>' or an opening '<a '. In the former case, the entity
	 * is already linked, in the latter, it isn't.
	 * @param $content string The text.
	 * @param $end int The offset in the text where the entity is found.
	 * @return bool True, if the entity is already linked, false otherwise.
	 */
	function already_linked($content, $end) {
		$link_end_occurrence   = strpos($content, '</a>', $end);
		$link_start_occurrence = strpos($content, '<a ', $end);
		if ($link_end_occurrence === false)
			return false;
		if ($link_start_occurrence === false)
			return true;
		return $link_end_occurrence - $link_start_occurrence < 0;

	}

} 