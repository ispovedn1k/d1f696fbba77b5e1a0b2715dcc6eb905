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
	$res = $db->query( "SELECT * FROM users WHERE id = 100;" );
	
	$rows = $res->fetch();
	
	echo "<pre>". var_dump($rows). "</pre>";
	
	echo "completed success";
}
catch (Exception $e) {
	echo $e->getMessage() . PHP_EOL;
	echo "[ ". $e->getLine() . " ] : ". $e->getFile() . PHP_EOL;
}