<?php

require_once 'secrets.php';

define('DOMAIN_NAME', "ispovedn1k.com");
define('ROOT_URI', "http://ispovedn1k.com/wg/");
define('QUEUE_SCRIPT_URI', "http://ispovedn1k.com/wg/2sdfhs3gsasd3.php?cont=queue");
define('QUEUE_ALLOWED_IP', "77.232.134.61");

if (! defined('__DIR__')) {
	define('__DIR__', dirname(__FILE__));
}

define('BASE_DIR', __DIR__ ."/");
define('VIEWS_DIR', BASE_DIR . "views/");
define('CONTROLLERS_DIR', BASE_DIR . "controllers/");
define('MODELS_DIR', BASE_DIR . "models/");
define('LIB_DIR', BASE_DIR . "lib/");

define('TABLE_PREFIX', '');

define('LANG', 'ru');

define('QUEUE_MAX_TRIES', 100);