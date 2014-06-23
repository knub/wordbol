<?php

namespace WordPressStanbol;

use WordPressStanbol\Models\EntityType;

class AdminHtml {

	public static function runStanbolButtonHtml() {
		return <<<TEXT
		<div class="misc-pub-section my-options">
			<input name="enhancement_button" type="submit" class="button button-primary button-large" id="enhancement_button" value="Enhance with selected entities">
		</div>
TEXT;
	}
	public static function stanbolSelectionHtml($annotations, $post_content, $selected_locations) {
		$annotations->rewind();
		$content = <<<HTML
			<h2>Recognized entities</h2>
			<p>Select entities to create a link for, then click "Enhance with selected entities":</p>
			<div id="wordpress-stanbol-entities">
			<script type="text/javascript">
				var places = [];
			</script>
HTML;
		$placeContent = <<<PLACE
			<h2>Overview about places mentioned</h2>
			<div id="map-canvas"></div>
			<br />
PLACE;
		$form_value = 0;
		$already_seen_resources = array();
		$already_seen_locations = array();
		$location_resources = array_map(function($location) { return $location->resource; }, $selected_locations);

		foreach ($selected_locations as $location) {
			array_push($already_seen_locations, $location->resource);
			$form_value += 1;
			$placeContent .= <<<MAPS
				<script type="text/javascript">
					places.push({
						address: "{$location->text}",
						id: "place$form_value",
						resource: "{$location->resource}",
						selected: true
					});
				</script>
				<input type="checkbox" name="place_location[]" id="place$form_value" value="$location->resource" checked="checked" />
				<label for="place$form_value">
					<div>{$location->text}</div>
				</label>
MAPS;
		}
		while ($annotations->valid()) {
			$text = $annotations->current();
			$entities = $annotations->getInfo();
			$annotations->next();
			if (count($entities) === 0)
				continue;
			$entity = $entities[0];
			$resource = $entity->get_resource();
			if (in_array($resource, $already_seen_resources))
				continue;

			array_push($already_seen_resources, $resource);
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
			if ($entity->get_entity_type() === EntityType::Place && !in_array($resource, $already_seen_locations)) {
				$checked = "";
				$selected = "false";
				if (in_array($resource, $location_resources) || count($selected_locations) == 0) {
					$checked = 'checked="checked"';
					$selected = "true";
				}
				$placeContent .= <<<MAPS
					<script type="text/javascript">
						places.push({
							address: "{$text->get_text()}",
							id: "place$form_value",
							resource: "$resource",
							selected: $selected
						});
					</script>
					<input type="checkbox" name="place_location[]" id="place$form_value" value="$resource" $checked />
					<label for="place$form_value">
						<div>{$text->get_text()}</div>
					</label>
MAPS;
			}
			$form_value += 1;
			$annotations->next();
		}
		$content .= <<<END
			<br style="clear: both" />
			$placeContent
		</div>
END;
		return $content;
	}

	private static function get_surrounding_text($text, $post_content) {
		$snippet_window = 700000000;
		$snippet_start = max(0, $text->get_start() - $snippet_window);
		$snippet = mb_substr($post_content, $snippet_start, $snippet_start === 0 ? $text->get_start() : $snippet_window);
		$snippet .= '<strongxxx>' . mb_substr($post_content, $text->get_start(), $text->length()) . '</strongxxx>';
		$snippet .= mb_substr($post_content, $text->get_start() + $text->length(), $snippet_window);
		$snippet =  strip_tags($snippet, '<strongxxx>');

		$snippet_size = 120;
		$index_start = max(strpos($snippet, '<strongxxx>') - $snippet_size, 0);
		$snippet_length = strpos($snippet, '</strongxxx>') - $index_start + $snippet_size;
//		wp_die("$index_start $index_end");
		return '…' . str_replace('</strongxxx>', '</strong>', str_replace('<strongxxx>', '<strong>', mb_substr($snippet, $index_start, $snippet_length))) . '…';
	}
}
