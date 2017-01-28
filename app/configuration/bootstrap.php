<?php

define("API_HOST", "opencounter-slim-codenv-webserver:8080");

if (!defined('APP_ROOT')) {
  $spl = new SplFileInfo(__DIR__ . '/..');
  define('APP_ROOT', $spl->getRealPath());
}


if (PHP_SAPI == 'cli-server') {
  // To help the built-in PHP dev server, check if the request was actually for
  // something which should probably be served as a static file
  $url = parse_url($_SERVER['REQUEST_URI']);
  $file = __DIR__ . $url['path'];
  if (is_file($file)) {
    return FALSE;
  }
}

require APP_ROOT . '/vendor/autoload.php';


session_start();

// ensure required environment variables are available
$dotenv = new Dotenv\Dotenv(APP_ROOT);
$dotenv->load();
$dotenv->required('DB_HOST');
$dotenv->required('MYSQL_DATABASE');
$dotenv->required('MYSQL_USER');
$dotenv->required('MYSQL_PASSWORD');


// Instantiate the app
$settings = require APP_ROOT . '/configuration/settings.php';

$app = new \Slim\App($settings);


// Set up dependencies
require APP_ROOT . '/configuration/dependencies.php';

// Register middleware
require APP_ROOT . '/configuration/middleware.php';


// Register routes
require APP_ROOT . '/configuration/routes.php';

