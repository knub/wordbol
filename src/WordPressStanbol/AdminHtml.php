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
		$content = '<h2>I found the following entities</h2><div id="wordpress-stanbol-entities">';
		$form_value = 0;
		while ($annotations->valid()) {
			$text = $annotations->current();
			$entity = $annotations->getInfo()[0];
			$resource = $entity->get_resource();
				$content .= <<<TEXT
			<input type="checkbox" name="entity_enhancement[]" id="enhancement$form_value" value="$resource" />
			<label for="enhancement$form_value"><div>{$text->get_text()} is <a href="$resource">$resource</a><div class="wordpress-stanbol-entities-info">Thisisjustsomeinfo.</div></div></label>
TEXT;
			$form_value += 1;
			$annotations->next();
		}
		$content .= '</div>';
		return $content;
	}
}
