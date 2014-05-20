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

	function __construct($confidence) {
		$this->set_confidence($confidence);
	}
	/**
	 * @return double The confidence for this enhancement as a double, as returned by stanbol.
	 */
	public function get_confidence() {
		return $this->confidence;
	}

	/**
	 * @param $confidence double The confidence for this enhancement.
	 */
	public function set_confidence($confidence) {
		$this->confidence = $confidence;
	}
}
?>