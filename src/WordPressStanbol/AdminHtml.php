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
		$content = '<h3>I found the following entities</h3><div id="wordpress-stanbol-entities">';
		$form_value = 0;
		while ($annotations->valid()) {
			$text = $annotations->current();
			$entity = $annotations->getInfo()[0];
				$content .= <<<TEXT
			<input type="checkbox" name="entityEnhancement[]" id="enhancement$form_value" value="$form_value" />
			<label for="enhancement$form_value"><div>{$text->get_text()} is <a href="{$entity->get_resource()}">{$entity->get_resource()}</a></div></label>
TEXT;
			$form_value += 1;
			$annotations->next();
		}
		$content .= '</div>';
		return $content;
	}
}
