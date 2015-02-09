<?php

require_once 'secrets.php';

define('DOMAIN_NAME', "ispovedn1k.com");
define('ROOT_URI', "http://ispovedn1k.com/wg/");
// лучше использовать случайный хэш, и переименовать backgroun_queue.php, чтобы не знали, как достучаться
define('QUEUE_SCRIPT_URI', "http://ispovedn1k.com/wg/background_queue.php?cont=queue");
define('QUEUE_ALLOWED_IP', "127.0.0.1");

if (! defined('__DIR__')) {
	define('__DIR__', dirname(__FILE__));
}

define('BASE_DIR', __DIR__ ."/");
define('VIEWS_DIR', BASE_DIR . "views/");
define('CONTROLLERS_DIR', BASE_DIR . "controllers/");
define('MODELS_DIR', BASE_DIR . "models/");
define('LIB_DIR', BASE_DIR . "lib/");
define('LOG_DIR', BASE_DIR . "logs/");

define('TABLE_PREFIX', '');

define('LANG', 'ru');

define('QUEUE_MAX_TRIES', 100);