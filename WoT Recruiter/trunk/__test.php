<?php
require_once 'config.php';
require_once 'lib/functions.php';

echo PHP_EOL . "CONSoLE TEST". PHP_EOL . PHP_EOL;
echo "==============================================". PHP_EOL . PHP_EOL;


try {
	$engine = Engine::getInstance();
	
	$db = $engine->db;
	
	$info = UsersVehiclesStatStrict::RequestVehiclesStatInfo(3916664, "373e2b193684c2a1fb3fd516fb33e715bb5a53d6");
	
	echo "Request to WG completed. Now saving...";
	
	$res = UsersVehiclesStatStrict::SaveVehiclesStatInfo( $info );
	
	echo "<pre>". print_r($res, true). "</pre>";
	
	echo "completed success";
}
catch (Exception $e) {
	echo $e->getMessage() . PHP_EOL;
	echo "[ ". $e->getLine() . " ] : ". $e->getFile() . PHP_EOL;
}