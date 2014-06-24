<?php
/**
* Plugin Name: WordPress Stanbol
* Description: Integration of Apache Stanbol in WordPress.
* Version: 0.1
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

$enhancer = new WordPressStanbol\StanbolEnhancer();
add_action('admin_head', function () {
	wp_enqueue_script('knubtip', '/wp-content/plugins/wordpress-stanbol/js/jquery.knubtip.js', array('jquery'));
	wp_enqueue_script('google-maps', 'http://maps.googleapis.com/maps/api/js?key=AIzaSyDJAdivC4VOwITwLhtG2Sji4hNFL72fQOY&sensor=false', $in_footxer = false);
	wp_enqueue_script('wordpress-stanbol-maps', '/wp-content/plugins/wordpress-stanbol/js/maps.js', array('jquery'));
	wp_enqueue_script('wordpress-stanbol', '/wp-content/plugins/wordpress-stanbol/js/admin-main.js', array('jquery'));
	wp_enqueue_style('wordpress-stanbol', '/wp-content/plugins/wordpress-stanbol/css/main.css');
});
add_action('wp_enqueue_scripts', function() {
	wp_enqueue_script('google-maps', 'http://maps.googleapis.com/maps/api/js?key=AIzaSyDJAdivC4VOwITwLhtG2Sji4hNFL72fQOY&sensor=false', $in_footxer = false);
	wp_enqueue_script('wordpress-stanbol-maps', '/wp-content/plugins/wordpress-stanbol/js/maps.js', array('jquery'));
	wp_enqueue_script('wordpress-stanbol-main', '/wp-content/plugins/wordpress-stanbol/js/main.js', array('jquery'));
	wp_enqueue_style('wordpress-stanbol', '/wp-content/plugins/wordpress-stanbol/css/main.css');
});


add_action('post_submitbox_misc_actions', function() {
	echo \WordPressStanbol\AdminHtml::runStanbolButtonHtml();
});
add_filter('the_content', function($content) {
	$post = $GLOBALS['post'];
	$id = $post->ID;
	$json = str_replace("\"", "\\\"", get_post_meta($id, 'locations', true));
	echo '<pre>';
	var_dump(json_decode(get_post_meta($id, 'locations', true)));
	echo '</pre>';
	$content .= <<<MAP
		<div id="map-canvas"></div>
		<script type="text/javascript">
			places = JSON.parse("$json");
		</script>
MAP;
	return $content;
});

add_action('wp_ajax_run_stanbol', function() use ($enhancer) {
	$id = $_GET['post_id'];
	$post = get_post($id);

	$post_content = $post->post_content;
	$enhancement_result = $enhancer->enhance($post_content);

	$json = get_post_meta($post->ID, 'locations', true);
	if ($json === "")
		$selected_locations = array();
	else
		$selected_locations = json_decode($json);
	echo \WordPressStanbol\AdminHtml::stanbolSelectionHtml($enhancement_result, $post_content, $selected_locations);
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

	// Add or Update the meta field in the database.
	if (!update_post_meta ($post_id, 'locations', $locations)) {
		add_post_meta($post_id, 'locations', $locations, true);
	};

	$content = get_post($post_id)->post_content;
	$integrator = new \WordPressStanbol\PostContentUpdater($content);
	$annotations = $enhancer->enhance($content)->get_entity_annotations();
	$annotations_to_be_removed = array();
	$tags = array();
	foreach ($annotations as $text) {
		$entities = $annotations[$text];
		if (count($entities) > 0) {
			if (!in_array($entities[0]->get_resource(), $selected_enhancements))
				array_push($annotations_to_be_removed, $text);
			else
				array_push($tags, $text->get_text());
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
	$prior_tags = wp_get_post_terms($post_id, 'post_tag', array("fields" => "names"));
	$tags = array_merge($tags, $prior_tags);
	wp_set_post_terms($post_id, $tags, 'post_tag');
};
add_action('save_post', 'integrate_stanbol_features');
?>

