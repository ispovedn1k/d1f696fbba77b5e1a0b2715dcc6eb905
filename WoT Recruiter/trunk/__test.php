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
	list($year, $month, $day) = explode("-", @date("Y-m-d"));
	$ip = $_SERVER['REMOTE_ADDR'];
	$sql = "UPDATE `". $db->tables("slackers") ."` SET
					`resources` = :resources,
					`ipSender` = '{$ip}'
				WHERE
					 `playerID` = :playerID AND `year` = {$year} AND `month` = {$month} AND `day` = {$day};";
	$pre = $db->prepare( $sql );
	if (! $pre ) {
		Log::put("sql error in ". $sql);
		throw new Exception("sql error");
	}
	
	$param = array(':resources' => 1492, ':playerID' => 29260861);
		
	if (! $pre->execute($param) ) 
	{
		Log::put("sql troubles: ". print_r($pre->errorInfo(), true) );
	}
		
	if ( $pre->rowCount() == 0) {
		echo "here i am!";
	}
	
	echo "<pre>". print_r($pre->errorInfo(), true). "</pre>";
	
	echo "completed success";
}
catch (Exception $e) {
	echo $e->getMessage() . PHP_EOL;
	echo "[ ". $e->getLine() . " ] : ". $e->getFile() . PHP_EOL;
}