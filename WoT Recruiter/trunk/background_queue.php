<?php
define ('BACKGROUND_QUEUE', 1);

require_once 'config.php';
require_once 'lib/functions.php';

$start_time = microtime(true);
$time = (int) $start_time;

$max_execution_time = ini_get("max_execution_time");

ignore_user_abort(1);


try {
	$engine = Engine::getInstance();
	
	$engine->execute();
}
catch (Exception $e) {
	echo $e->getMessage();
}