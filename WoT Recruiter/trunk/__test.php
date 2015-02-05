<?php
require_once 'config.php';
require_once 'lib/functions.php';

echo PHP_EOL . "CONSoLE TEST". PHP_EOL . PHP_EOL;
echo "==============================================". PHP_EOL . PHP_EOL;


try {
	$engine = Engine::getInstance();
	
	$db = $engine->db;
	
	$res = $db->query( "SELECT * FROM `candidates`;");
	
	$rows = $res->fetchAll();
	
	echo "<pre>". print_r($rows, true). "</pre>";
	
	echo "completed success";
}
catch (Exception $e) {
	echo $e->getMessage() . PHP_EOL;
	echo "[ ". $e->getLine() . " ] : ". $e->getFile() . PHP_EOL;
}