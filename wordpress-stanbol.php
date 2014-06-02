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
require 'src/WordPressStanbol/Models/EnhancementResult.php';
require 'src/WordPressStanbol/Models/TextAnnotation.php';
require 'src/WordPressStanbol/Models/Enhancement.php';
require 'src/WordPressStanbol/Models/LanguageEnhancement.php';
require 'src/WordPressStanbol/Models/EntityAnnotationEnhancement.php';

$enhancer = new WordPressStanbol\StanbolEnhancer();

add_action('post_submitbox_misc_actions', function() {
?>
	<div class="misc-pub-section my-options">
		<input name="stanbol" type="submit" class="button button-primary button-large" id="stanbol" value="Run enhancement">
	</div>
<?php
});
add_action('edit_form_after_editor', function($post) use ($enhancer) {
	echo '<input name="stanbol" type="text" id="stanbol" value="text" />';
	echo '<input name="text" type="submit" id="text" value="text" />';
//	echo '<pre>';
//	var_dump($_POST);
//	var_dump($enhancer->enhance($post->post_content));
//	echo '</pre>';
});
function integrate_stanbol_features($post_id) {
	global $enhancer;
	$post = get_post($post_id);
	$content = $post->post_content;
	remove_action('save_post', 'integrate_stanbol_features');
	$result = $enhancer->enhance($content)->get_entity_annotations();
	$result->rewind();
	while ($result->valid()) {
		$text_annotation = $result->current();
		$entity_annotation = $result->getInfo();
		$link = $entity_annotation[0]->get_resource();
		$content = substr_replace($content, '</a>', $text_annotation->get_end() + 1, 0);
		$content = substr_replace($content, "<a href='$link'>", $text_annotation->get_start(), 0);
		break;

	}
	wp_update_post(array('ID' => $post_id, 'post_content' => $content) );
//	echo '<pre>';
//	var_dump($_POST);
//	wp_die("Stop here.");
//	echo '</pre>';
	add_action('save_post', 'integrate_stanbol_features');
};
add_action('save_post', 'integrate_stanbol_features');

echo '<br /><br /><br /><pre>';
//var_dump($enhancer->enhance('The Stanbol enhancer can detect famous cities such as Paris and people such as Bob Marley. In Deutschland denkt man aber anders. Deutschland.'));
echo '</pre>';

?>

