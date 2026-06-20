<?php

declare(strict_types=1);

use App\Http\Session;
use Dotenv\Dotenv;

define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/vendor/autoload.php';

if (is_file(APP_ROOT . '/.env')) {
    Dotenv::createImmutable(APP_ROOT)->safeLoad();
}

$environment = (string) env_value('APP_ENV', 'production');
$debug = (bool) env_value('APP_DEBUG', false);

date_default_timezone_set((string) env_value('APP_TIMEZONE', 'UTC'));

if ($environment === 'local' || $debug === true) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

Session::start();
