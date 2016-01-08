<?php
define ('BACKGROUND_QUEUE', 1);

require_once 'config.php';
require_once 'lib/functions.php';

$start_time = microtime(true);
$time = (int) $start_time;

$max_execution_time = ini_get("max_execution_time");

ignore_user_abort(1);

/**
 * Для nginx потребовалось включение опции fastcgi_ignore_client_abort on;
 */



try {
	$engine = Engine::getInstance();
	
	$engine->execute();
}
catch (Exception $e) {
	echo "EPIC FAIL";
	
	$file_content = $e->getMessage() . PHP_EOL;
	$file_content .= "[ ". $e->getLine() ." ]". $e->getFile() . PHP_EOL;
	$file_content .= '$_GET'. PHP_EOL. print_r( $_GET, true ) . PHP_EOL;
	$file_content .= '$_POST'. PHP_EOL. print_r( $_POST, true ) . PHP_EOL;
	$file_content .= '$_SERVER'. PHP_EOL. print_r( $_SERVER, true ) . PHP_EOL;
	$file_content .= '$_COOKIES'. PHP_EOL. print_r( $_COOKIE, true ) . PHP_EOL;
	$file_content .= '$engine'. PHP_EOL. print_r( $engine, true ) . PHP_EOL;
	$file_content .= 'lastQuery: '. $engine->db->getLastQuery() . PHP_EOL;
	$file_content .= 'logDump:'. PHP_EOL . print_r (LOG::getAll(), true) . PHP_EOL;
	
	@file_put_contents( LOG_DIR . @date("Y-m-d_H-i-s") .".log", $file_content);
}