<?php
$start_time = microtime(true);

require_once 'config.php';

require_once 'lib/functions.php';

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
	
	@file_put_contents( LOG_DIR . @date("Y-m-d_H-i-s") .".log", $file_content);
}