<?php
namespace WordPressStanbol\Models;
/**
 * Base class for all kinds of enhancements.
 * See https://stanbol.apache.org/docs/trunk/components/enhancer/enhancementstructure for details on Stanbol's
 * enhancements.
 * @package WordPressStanbol\Models
 */
class Enhancement {
	private $confidence;

	/**
	 * @return double The confidence for this enhancement as a double, as returned by stanbol.
	 */
	public function getConfidence() {
		return $this->confidence;
	}

	/**
	 * @param $confidence double The confidence for this enhancement.
	 */
	public function setConfidence($confidence) {
		$this->confidence = $confidence;
	}
}
?>