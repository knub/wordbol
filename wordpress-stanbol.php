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
require 'src/WordPressStanbol/StanbolEnhancer.php';
require 'src/WordPressStanbol/AdminHtml.php';
require 'src/WordPressStanbol/PostContentUpdater.php';
require 'src/WordPressStanbol/Models/EnhancementResult.php';
require 'src/WordPressStanbol/Models/TextAnnotation.php';
require 'src/WordPressStanbol/Models/Enhancement.php';
require 'src/WordPressStanbol/Models/LanguageEnhancement.php';
require 'src/WordPressStanbol/Models/EntityAnnotationEnhancement.php';

$enhancer = new WordPressStanbol\StanbolEnhancer();
add_action('admin_head', function (){
	wp_enqueue_script('wordpress-stanbol', '/wp-content/plugins/wordpress-stanbol/js/main.js', array('jquery'));
	wp_enqueue_style('wordpress-stanbol', '/wp-content/plugins/wordpress-stanbol/css/main.css');
});


add_action('post_submitbox_misc_actions', function() {
	echo \WordPressStanbol\AdminHtml::runStanbolButtonHtml();
});
add_action('edit_form_after_editor', function($post) use ($enhancer) {
//	echo '<pre>';
//	var_dump($post);
//	echo '</pre>';
	$content = $post->post_content;
	$annotations = $enhancer->enhance($content)->get_entity_annotations();
	echo \WordPressStanbol\AdminHtml::stanbolSelectionHtml($annotations);
});
function integrate_stanbol_features($post_id) {
	global $enhancer;
	if (!isset($_POST['enhancement']))
		return;
	$content = get_post($post_id)->post_content;
	remove_action('save_post', 'integrate_stanbol_features');
	$annotations = $enhancer->enhance($content)->get_entity_annotations();
	$integrator = new \WordPressStanbol\PostContentUpdater($content);
	$content = $integrator->integrate_annotations($annotations);
//	wp_update_post(array('ID' => $post_id, 'post_content' => $content));
//	echo '<pre>';
//	var_dump($_POST);
//	wp_die("Stop here.");
//	echo '</pre>';
	add_action('save_post', 'integrate_stanbol_features');
};
add_action('save_post', 'integrate_stanbol_features');
?>

