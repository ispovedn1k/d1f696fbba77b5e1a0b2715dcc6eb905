<?php

require_once 'config.php';

require_once 'lib/functions.php';

try {
	$engine = Engine::getInstance();
	
	$engine->execute();
}
catch (Exception $e) {
	echo $e->getMessage();
}