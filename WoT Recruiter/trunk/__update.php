<?php
require_once 'config.php';
require_once 'lib/functions.php';

echo PHP_EOL . "CONSoLE installation". PHP_EOL . PHP_EOL;
echo "==============================================". PHP_EOL . PHP_EOL;


try {
	$db = Engine::getInstance()->db;
	
	echo "DONE! Completed successfully!". PHP_EOL;
}
catch (Exception $e) {
	echo $e->getMessage() . PHP_EOL;
	echo "[ ". $e->getLine() . " ] : ". $e->getFile() . PHP_EOL;
}