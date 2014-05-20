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
require 'src/WordPressStanbol/Models/Enhancement.php';
require 'src/WordPressStanbol/Models/LanguageEnhancement.php';

$enhancer = new WordPressStanbol\StanbolEnhancer();
echo '<br /><br /><br /><pre>';
var_dump($enhancer->enhance('The Stanbol enhancer can detect famous cities such as Paris and people such as Bob Marley. In Deutschland denkt man aber anders. Deutschland.'));
echo '</pre>';

//$response = wp_remote_post("http://localhost:8080/enhancer/", array(
//	'httpversion' => '1.1',
//	'headers' => array(
//		//'Accept' => 'application/rdf+json',
//		'Accept' => 'text/turtle',
//		'Content-Type' => 'application/x-www-form-urlencoded',
//	),
//	'body' => array(
//		'content' =>
//	),
//));
//if ( is_wp_error( $response ) ) {
//	$error_message = $response->get_error_message();
//	echo "Something went wrong: $error_message";
//} else {
//	echo '<br /><br /><br /><br /><br /><br /><br /><br /><br />';
//	$graph = new EasyRdf_Graph();
//	$graph->parse($response['body'], 'turtle');
//	$resources = $graph->allOfType('http://fise.iks-project.eu/ontology/Enhancement');
//	echo '<ul>';
//	array_walk($resources, function($resource) {
//		echo '<li>' . $resource . '</li>';
//	});
//	echo '<pre>';
//	print_r($resources);
//	echo '</pre>';
//	echo '</ul>';
//}
?>

