<?php
define('STANBOL_INSTANCE', 'http://localhost:8080/enhancer/');
define('MINIMUM_CONFIDENCE', 0.0);

function debug_print($v) {
	echo '<pre>';
	var_dump($v);
	echo '</pre>';
	wp_die();
}
//define('STANBOL_INSTANCE', 'http://localhost:8080/enhancer/chain/dbpedia-disambiguation');
?>