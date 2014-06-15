<?php

namespace WordPressStanbol;

use WordPressStanbol\Models\EntityType;

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
			<script type="text/javascript">
				var places = [];
			</script>
HTML;
		$form_value = 0;
		while ($annotations->valid()) {
			$text = $annotations->current();
			if (count($annotations->getInfo()) === 0) {
				$annotations->next();
				continue;
			}
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
			if ($entity->get_entity_type() === EntityType::Place) {
				$content .= <<<MAPS
					<script type="text/javascript">
						places.push("{$text->get_text()}");
					</script>
MAPS;
			}
			$form_value += 1;
			$annotations->next();
		}
		$content .= <<<END
		</div>
		<br style="clear: both" />
		<div id="map-canvas"></div>
END;
		return $content;
	}

	private static function get_surrounding_text($text, $post_content) {
		$snippet_window = 700000000;
		$snippet_start = max(0, $text->get_start() - $snippet_window);
		$snippet = substr($post_content, $snippet_start, $snippet_start === 0 ? $text->get_start() : $snippet_window);
		$snippet .= '<strongxxx>' . substr($post_content, $text->get_start(), $text->length()) . '</strongxxx>';
		$snippet .= substr($post_content, $text->get_start() + $text->length(), $snippet_window);
		$snippet =  strip_tags($snippet, '<strongxxx>');

		$snippet_size = 120;
		$index_start = max(strpos($snippet, '<strongxxx>') - $snippet_size, 0);
		$snippet_length = strpos($snippet, '</strongxxx>') - $index_start + $snippet_size;
//		wp_die("$index_start $index_end");
		return '…' . str_replace('</strongxxx>', '</strong>', str_replace('<strongxxx>', '<strong>', substr($snippet, $index_start, $snippet_length))) . '…';
	}
}
