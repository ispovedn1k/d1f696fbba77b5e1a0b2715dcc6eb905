<?php
require_once 'config.php';
require_once 'lib/functions.php';

echo PHP_EOL . "CONSoLE TEST". PHP_EOL . PHP_EOL;
echo "==============================================". PHP_EOL . PHP_EOL;


try {
	$engine = Engine::getInstance();
	
	$db = $engine->db;
	
	//$rows = UsersVehiclesStatStrict::RequestVehiclesStatInfo( 3916664, "34fc55913d3b795052af46310e60f666f6c73182" );
	
	//UsersVehiclesStatStrict::SaveVehiclesStatInfo( $rows );
	$res = $db->query( "SELECT * FROM `queue`;" );
	
	$rows = $res->fetchAll(PDO::FETCH_ASSOC);
	
	echo "<pre>". print_r($rows, true). "</pre>";
	
	echo "completed success";
}
catch (Exception $e) {
	echo $e->getMessage() . PHP_EOL;
	echo "[ ". $e->getLine() . " ] : ". $e->getFile() . PHP_EOL;
}