<?php

namespace WordPressStanbol;

class AdminHtml {

	public static function runStanbolButtonHtml() {
		return <<<TEXT
		<div class="misc-pub-section my-options">
			<input name="enhancement" type="submit" class="button button-primary button-large" id="enhancement" value="Enhance with selected entities">
		</div>
TEXT;
	}
	public static function stanbolSelectionHtml($annotations) {
		$annotations->rewind();
		$content = <<<HTML
			<h2>Recognized entities</h2>
			<p>Select entities to create a link for, then click "Enhance with selected entities":</p>
			<div id="wordpress-stanbol-entities">
HTML;
		$form_value = 0;
		while ($annotations->valid()) {
			$text = $annotations->current();
			if (count($annotations->getInfo()) === 0) {
				echo '<pre>';
				var_dump($text);
				var_dump($annotations->getInfo());
				echo '</pre>';
				wp_die("STOP");
				$annotations->next();
				continue;
			}
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
