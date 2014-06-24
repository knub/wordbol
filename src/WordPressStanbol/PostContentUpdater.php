<?php

namespace WordPressStanbol;


/**
 * This class integrates the given entities into the original content.
 * It takes care, that no entity is linked twice within the same document.
 * @package WordPressStanbol
 */
class PostContentUpdater {

	private $content;
	private $offset = 0;

	private $already_seen_resources = array();
	private $links = array();

	function __construct($content) {
		$this->content = $content;

		// determine all links so far, so that no link is made twice
		$this->links = Helper::getLinks($content);
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
		$texts = array();
		while ($annotations->valid()) {
			$text = $annotations->current();
			array_push($texts, $text);
			$annotations->next();
		}
		usort($texts, function($k1, $k2) {
			return $k1->get_start() - $k2->get_start();
		});

		$this->already_seen_resources = array_merge(array(), $this->links);
		foreach ($texts as $text) {
			$entity = $annotations[$text];
			$this->content = $this->integrate_annotation($text, $entity);
		}

		return $this->content;
	}

	function integrate_annotation($text, $entity) {
		if (count($entity) === 0)
			return $this->content;
		// get best fitting resource --> $entity[0]
		$resource = $entity[0]->get_resource();

		$link = str_replace("dbpedia.org/resource", "en.wikipedia.org/wiki", $resource);
		// take care, that no resource is linked twice!
		if (in_array($link, $this->already_seen_resources))
			return $this->content;
		array_push($this->already_seen_resources, $link);

		$end = $text->get_end();
		$content = $this->content;
		$string_beginning = "<a href='$link'>";
		$string_end = '</a>';
		$content = substr_replace($content, $string_end, $end + $this->offset, 0);
		$content = substr_replace($content, $string_beginning, $text->get_start() + $this->offset, 0);
		$this->offset += strlen($string_beginning) + strlen($string_end);
		return $content;
	}
}