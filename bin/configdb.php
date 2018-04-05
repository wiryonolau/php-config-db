<?php

use Zend\Mvc\Application as MvcApplication;
use Zend\Stdlib\ArrayUtils;

use Zend\Console\Console;
use ZF\Console\Dispatcher;
use ZF\Console\Route;
use ZF\Console\Application as ConsoleApplication;

date_default_timezone_set('UTC');

DEFINE("APP_ENV", getenv('APP_ENV') ? getenv('APP_ENV') : 'production');
DEFINE("APP_PATH", getcwd());
DEFINE("APP_CLI", php_sapi_name() === 'cli');
DEFINE("APP_TYPE", "admin");

if (APP_ENV == 'development') {

    error_reporting(E_ALL);
    ini_set('display_errors', true);
}

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Composer autoloading
include APP_PATH. '/vendor/autoload.php';

if (! class_exists(MvcApplication::class)) {
    throw new RuntimeException(
        "Unable to load application.\n"
        . "- Type `composer install` if you are developing locally.\n"
        . "- Type `vagrant ssh -c 'composer install'` if you are using Vagrant.\n"
        . "- Type `docker-compose run zf composer install` if you are using Docker.\n"
    );
}

// Retrieve configuration
$appConfig = require APP_PATH. '/config/application.config.php';
if (file_exists(APP_PATH . '/config/development.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, require APP_PATH .'/config/development.config.php');
}

if(!isset($appConfig["modules"])) {
    $appConfig["modules"] = array();
}

foreach(["Zend\Router", "ConfigDB"] as $required_module) {
    if (in_array($required_module, $appConfig["modules"])) {
        continue;
    }

    $appConfig["modules"][] = $required_module;
}

// Init Application combine all module configuration
$application = MvcApplication::init($appConfig);


$serviceManager = $application->getServiceManager();
$config = $serviceManager->get("config");

$console = Console::getInstance();

if (empty($config["console"]["router"]["routes"])) {
    $console->writeLine("No console config defined");
    exit(1);
}

// Only allow zf-console format
$console_config = array_filter($config["console"]["router"]["routes"], function($c) {
    return (!empty($c["name"]));
});

if (empty($console_config)) {
    $console->writeLine("No console config defined");
    exit(1);
}


$application = new ConsoleApplication(
    'ConfigDB',
    '0.1',
    $console_config,
    $console,
    new Dispatcher($serviceManager)
);

$application->removeRoute("autocomplete");
$application->removeRoute("version");

$exit = $application->run();
exit($exit);
