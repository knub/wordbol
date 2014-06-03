<?php

namespace WordPressStanbol;

class AdminHtml {

	public static function runStanbolButtonHtml() {
		return <<<TEXT
		<div class="misc-pub-section my-options">
			<input name="enhancement" type="submit" class="button button-primary button-large" id="enhancement" value="Run enhancement">
		</div>
TEXT;
	}
	public static function stanbolSelectionHtml($annotations) {
		$annotations->rewind();
		$content = '<div id="wordpress-stanbol-entities">';
		$form_value = 0;
		while ($annotations->valid()) {
			$text = $annotations->current();
			$entity = $annotations->getInfo()[0];
				$content .= <<<TEXT
			<input type="checkbox" name="entityEnhancement[]" id="enhancement$form_value" value="$form_value" />
			<label for="enhancement$form_value">{$text->get_text()} is {$entity->get_resource()}</label>
			<br />
TEXT;
			$form_value += 1;
			$annotations->next();
		}
		$content .= '</div>';
		return $content;
	}
}
