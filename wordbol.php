<?php
/**
* Plugin Name: Wordbol
* Description: Integration of Apache Stanbol in WordPress.
* Version: 1.1
* Author: Stefan Bunk
* License: MIT
*/

/**  The MIT License (MIT)

    Copyright (c) 2014 Stefan Bunk

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
    THE SOFTWARE.
*/

error_reporting(E_ALL);
require_once 'config.php';
require 'vendor/autoload.php';

function debug_print($v, $die = true) {
	echo '<pre>';
	var_dump($v);
	echo '</pre>';
	if ($die)
		wp_die();
}

$enhancer = new Wordbol\StanbolEnhancer();
add_action('admin_head', function () {
	wp_enqueue_script('knubtip', '/wp-content/plugins/wordbol/js/jquery.knubtip.js', array('jquery'));
	// yes, you have to do this duplicated code
	if (!is_null(GOOGLE_MAPS_API_KEY))
		wp_enqueue_script('google-maps', 'http://maps.googleapis.com/maps/api/js?key=' . GOOGLE_MAPS_API_KEY . '&sensor=false');
	wp_enqueue_script('wordbol-maps', '/wp-content/plugins/wordbol/js/maps.js', array('jquery'));
	wp_enqueue_script('wordbol', '/wp-content/plugins/wordbol/js/admin-main.js', array('jquery'));
	wp_enqueue_style('wordbol', '/wp-content/plugins/wordbol/css/main.css');
});
add_action('wp_enqueue_scripts', function() {
	if (!is_null(GOOGLE_MAPS_API_KEY))
		wp_enqueue_script('google-maps', 'http://maps.googleapis.com/maps/api/js?key=' . GOOGLE_MAPS_API_KEY . '&sensor=false');
	wp_enqueue_script('wordbol-maps', '/wp-content/plugins/wordbol/js/maps.js', array('jquery'));
	wp_enqueue_script('wordbol-main', '/wp-content/plugins/wordbol/js/main.js', array('jquery'));
	wp_enqueue_style('wordbol', '/wp-content/plugins/wordbol/css/main.css');
});

add_action('post_submitbox_misc_actions', function() {
	echo \Wordbol\AdminHtml::runStanbolButtonHtml();
});
$map_counter = 0;
add_filter('the_content', function($content) {
	global $map_counter;
	$post = $GLOBALS['post'];
	$id = $post->ID;
	$json = str_replace("\"", "\\\"", get_post_meta($id, 'locations', true));
	if (!is_null(GOOGLE_MAPS_API_KEY)) {
		$content .= <<<MAP
			<div id="map-canvas$map_counter" class="map-canvas"></div>
			<script type="text/javascript">
				if (typeof(allPlaces) === "undefined") {
					allPlaces = [];
				}
				var json = "$json";
				if (json.length !== 0)
					allPlaces.push(JSON.parse(json));
			</script>
MAP;
		$map_counter += 1;
	}
	return $content;
});

add_action('wp_ajax_run_stanbol', function() use ($enhancer) {
	$id = $_GET['post_id'];
	$post = get_post($id);

	$post_content = $post->post_content;
	try {
		$enhancement_result = $enhancer->enhance($post_content);
		$json = get_post_meta($post->ID, 'locations', true);
		if ($json === "")
			$selected_locations = array();
		else
			$selected_locations = json_decode($json);
		echo \Wordbol\AdminHtml::stanbolSelectionHtml($enhancement_result, $post_content, $selected_locations);
	}
	catch (\Wordbol\StanbolNotAccessibleException $e) {
		echo '<h2>Error: Could not reach Stanbol. Please check, that you started Stanbol and it is reachable over the network.</h2>';
		echo '<pre>';
		echo $e->getMessage();
		echo $e->getTraceAsString();
		echo '</pre>';
	}
	wp_die();
});

add_action('edit_form_after_editor', function($post) use ($enhancer) {
	$id = $post->ID;
	$url = plugins_url('css/loader.gif', __FILE__);
	echo <<<SCRIPT
		<script type="text/javascript">
			var POST_ID = $id;
		</script>
		<div id="stanbol_content">
			<h2>Loading Stanbol Entities</h2>
			<img src="$url" alt="Loader" />
		</div>
SCRIPT;
});

function integrate_stanbol_features($post_id) {
	global $enhancer;
	if (!isset($_POST['enhancement_button']))
		return;
	$selected_enhancements = [];
	if (isset($_POST['entity_enhancement']))
		$selected_enhancements = $_POST['entity_enhancement'];
	$selected_locations = [];
	if (isset($_POST['place_location']))
		$selected_locations = $_POST['place_location'];
	$locations = [];
	foreach ($selected_locations as $location) {
		$decoded = json_decode(str_replace("\\", "", $location));
		if ($decoded != null)
			array_push($locations, $decoded);
	}
	$locations = json_encode($locations);

	// Add or Update the location field in the database.
	if (!update_post_meta ($post_id, 'locations', $locations)) {
		add_post_meta($post_id, 'locations', $locations, true);
	};

	$content = get_post($post_id)->post_content;
	$integrator = new \Wordbol\PostContentUpdater($content);
	$annotations = $enhancer->enhance($content)->get_entity_annotations();
	$annotations_to_be_removed = array();
	$tags = array();
	foreach ($annotations as $text) {
		$entities = $annotations[$text];
		if (count($entities) > 0) {
			$entity = $entities[0];
			if (!in_array($entity->get_resource(), $selected_enhancements))
				array_push($annotations_to_be_removed, $text);
			else {
				if ($text->get_confidence() > MINIMUM_CONFIDENCE) {
					array_push($tags, $text->get_text());
				}
			}
		}
	}
	foreach ($annotations_to_be_removed as $remove) {
		$annotations->detach($remove);
	}
	$content = $integrator->integrate_annotations($annotations);
	// remove action to avoid recursion
	remove_action('save_post', 'integrate_stanbol_features');
	wp_update_post(array('ID' => $post_id, 'post_content' => $content));
	// Add action again
	add_action('save_post', 'integrate_stanbol_features');

	// Set tags
	if (AUTO_TAG) {
		$prior_tags = wp_get_post_terms($post_id, 'post_tag', array("fields" => "names"));
		$tags = array_merge($tags, $prior_tags);
		wp_set_post_terms($post_id, $tags, 'post_tag');
	}
};
add_action('save_post', 'integrate_stanbol_features');
