<?php
require_once 'config.php';
require_once 'lib/functions.php';

echo PHP_EOL . "CONSoLE TEST". PHP_EOL . PHP_EOL;
echo "==============================================". PHP_EOL . PHP_EOL;


try {
	$engine = Engine::getInstance();
	
	$db = $engine->db;
	
	$data = array(
			array(
					':one' => 1,
					':two' => 2,
			),
			array(
					':one' => 11,
					':two' => 22,
			),
			array(
					':one' => 111,
					':two' => 222,
			),
			array(
					':one' => 1111,
					':two' => 2222,
			),
			array(
					':one' => 11111,
					':two' => 22222,
			),
			array(
					':one' => 111111,
					':two' => 222222,
			),
	);
	
	$pre = $db->prepare ("UPDATE `testtable` SET `one` = :one, `two` = :two WHERE `two` = :two;");
	if (! $pre) {
		throw new Exception("Prepare failed");
	}
	
	foreach ($data as $row) {
		if (! $pre->execute($row)) {
			echo "fail execution\r\n";
		}
		else {
			echo "success step\r\n";
		}
	}
	
	echo "completed success";
}
catch (Exception $e) {
	echo $e->getMessage() . PHP_EOL;
	echo "[ ". $e->getLine() . " ] : ". $e->getFile() . PHP_EOL;
}