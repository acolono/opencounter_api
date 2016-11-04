<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// ensure required environment variables are available
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();
$dotenv->required('DB_HOST');
$dotenv->required('MYSQL_DATABASE');
$dotenv->required('MYSQL_USER');
$dotenv->required('MYSQL_PASSWORD');

// Instantiate the app
$settings = require __DIR__ . '/../configuration/settings.php';

$app = new \Slim\App($settings);


// Set up dependencies
require __DIR__ . '/../configuration/dependencies.php';

// Register middleware
require __DIR__ . '/../configuration/middleware.php';

// load constants (TODO: is this better than .env)
require __DIR__ . '/../configuration/constants.php';

// Register routes
require __DIR__ . '/../configuration/routes.php';

// Run app
$app->run();
