<?php

namespace Wordbol;


class Helper {
	public static function getLinks($content) {
		if ($content == "")
			return array();
		$doc = new \DOMDocument();
		$doc->loadHTML($content);
		$selector = new \DOMXPath($doc);
		$result = $selector->query('//a');
		$links = array();
		foreach($result as $node) {
			array_push($links, $node->getAttribute('href'));
		}
		$links = array_unique($links);
		return $links;
	}

} 
