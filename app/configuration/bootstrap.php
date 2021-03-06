<?php
/**
 * Bootstrapping the app
 *
 * Before we setup the app load settings and setup autoloader
 *
 * @see settings.php
 * @file
 */


if (PHP_SAPI == 'cli-server') {
  // To help the built-in PHP dev server, check if the request was actually for
  // something which should probably be served as a static file
  $url = parse_url($_SERVER['REQUEST_URI']);
  $file = __DIR__ . $url['path'];
  if (is_file($file)) {
    return FALSE;
  }
}

// setup autoloader
require __DIR__ . '/../vendor/autoload.php';


session_start();

$envFile = __DIR__ . "/../.env";
if (file_exists($envFile)) {
    $dotenv = new Dotenv\Dotenv(dirname($envFile));
    $dotenv->load();
    // ensure required environment variables are available
    $dotenv->required('DB_HOST');
    $dotenv->required('MYSQL_DATABASE');
    $dotenv->required('MYSQL_USER');
    $dotenv->required('MYSQL_PASSWORD');
}



/**
 * Setting a constant we will use later during swagger annotations
 */
define("API_HOST", getenv('API_HOST'));


// Instantiate the application with settings from file
$settings = require __DIR__ . '/settings.php';


$app = new \Slim\App($settings);

// No idea when best to initialize even subscribers. but before routes and controllers or before each request probably

//\Ddd\Domain\DomainEventPublisher::instance()->subscribe(
//  new \Ddd\Domain\PersistDomainEventSubscriber(
//    $app->getContainer()->get('event_store')
//  )
//);

