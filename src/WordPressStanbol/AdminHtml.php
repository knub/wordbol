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
	public static function stanbolSelectionHtml($annotations, $post_content) {
		$annotations->rewind();
		$content = <<<HTML
			<h2>Recognized entities</h2>
			<p>Select entities to create a link for, then click "Enhance with selected entities":</p>
			<div id="wordpress-stanbol-entities">
HTML;
		$form_value = 0;
		while ($annotations->valid()) {
			$text = $annotations->current();
			$entity = $annotations->getInfo()[0];
			$resource = $entity->get_resource();
			$type = $entity->get_entity_type();
			$surrounding_text = self::get_surrounding_text($text, $post_content);
			$confidence = round($entity->get_confidence() * 100, 0);
			$content .= <<<TEXT
			<input type="checkbox" name="entity_enhancement[]" id="enhancement$form_value" value="$resource" />
			<label for="enhancement$form_value">
				<div>
					{$text->get_text()}
					<div class="wordpress-stanbol-entities-info">
						<table>
							<tr>
								<td>Resource</td>
								<td><a href="$resource">$resource</a></td>
							</tr>
							<tr>
								<td>Context</td>
								<td>$surrounding_text</td>
							</tr>
							<tr>
								<td>Confidence</td>
								<td>$confidence %</td>
							</tr>
							<tr>
								<td>Entity Type</td>
								<td>$type</td>
							</tr>
						</table>
					</div>
				</div>
			</label>
TEXT;
			$form_value += 1;
			$annotations->next();
		}
		$content .= '</div><br style="clear: both" /><div id="map-canvas"></div>';
		return $content;
	}

	// TODO: Sophisticate
	private static function get_surrounding_text($text, $post_content) {
		$snippet_size = 70;
		$snippet_start = max(0, $text->get_start() - $snippet_size);
		$snippet = substr($post_content, $snippet_start, $snippet_start === 0 ? $text->get_start() : $snippet_size);
		$snippet .= '<strong>' . substr($post_content, $text->get_start(), $text->length()) . '</strong>';
		$snippet .= substr($post_content, $text->get_start() + $text->length(), $snippet_size);
		$snippet =  strip_tags($snippet, '<strong>');
		if ($snippet_start === 0)
			return $snippet;
		return $snippet;
//		return substr($snippet, 10, strlen($snippet) - 20);
	}
}
