<?php

use Zend\Stdlib\ArrayUtils;
use Zend\Console\Console;
use ZF\Console\Dispatcher;
use ZF\Console\Application as ConsoleApplication;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

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
include APP_PATH . '/vendor/autoload.php';

/* Setup Service Manager */
$modules = ["ConfigDB"];
$config = [];

foreach ($modules as $module) {
    $module_class = "$module\Module";
    $module = new $module_class();
    $config = ArrayUtils::merge($config, $module->getConfig());
}

$smConfig = isset($config['service_manager']) ? $config['service_manager'] : [];
$smConfig = new Config($smConfig);
$serviceManager = new ServiceManager();
$smConfig->configureServiceManager($serviceManager);
$serviceManager->setService('config', $config);

$console = Console::getInstance();

if (empty($config["console"]["router"]["routes"])) {
    $console->writeLine("No console config defined");
    exit(1);
}

// Only allow zf-console format
$console_config = array_filter($config["console"]["router"]["routes"],
        function($c) {
    return (!empty($c["name"]));
});

if (empty($console_config)) {
    $console->writeLine("No console config defined");
    exit(1);
}


$application = new ConsoleApplication(
        'ConfigDB', '0.1', $console_config, $console,
        new Dispatcher($serviceManager)
);

$application->removeRoute("autocomplete");
$application->removeRoute("version");

$exit = $application->run();
exit($exit);
