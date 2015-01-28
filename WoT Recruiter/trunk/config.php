<?php

require_once 'secrets.php';

define('DOMAIN_NAME', "ispovedn1k.com");
define('ROOT_URI', "http://ispovedn1k.com/wg/");

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