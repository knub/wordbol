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
		$content = $this->mb_substr_replace($content, $string_end, $end + $this->offset, 0);
		$content = $this->mb_substr_replace($content, $string_beginning, $text->get_start() + $this->offset, 0);
		$this->offset += strlen($string_beginning) + strlen($string_end);
		return $content;
	}

	// It is important to use the unicode version for all string manipulation
	// PHP does not have a mb_substr_replace function
	// So I copied the following function from here:
	// http://php.net/manual/ru/function.substr-replace.php#90146
	function mb_substr_replace($string, $replacement, $start, $length = null, $encoding = null)
	{
		if (extension_loaded('mbstring') === true)
		{
			$string_length = (is_null($encoding) === true) ? mb_strlen($string) : mb_strlen($string, $encoding);

			if ($start < 0)
			{
				$start = max(0, $string_length + $start);
			}

			else if ($start > $string_length)
			{
				$start = $string_length;
			}

			if ($length < 0)
			{
				$length = max(0, $string_length - $start + $length);
			}

			else if ((is_null($length) === true) || ($length > $string_length))
			{
				$length = $string_length;
			}

			if (($start + $length) > $string_length)
			{
				$length = $string_length - $start;
			}

			if (is_null($encoding) === true)
			{
				return mb_substr($string, 0, $start) . $replacement . mb_substr($string, $start + $length, $string_length - $start - $length);
			}

			return mb_substr($string, 0, $start, $encoding) . $replacement . mb_substr($string, $start + $length, $string_length - $start - $length, $encoding);
		}

		return (is_null($length) === true) ? substr_replace($string, $replacement, $start) : substr_replace($string, $replacement, $start, $length);
	}
}