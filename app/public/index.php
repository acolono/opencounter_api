<?php
/**
 * index.php
 *
 * Single point of entry
 *
 */

/**
 * Include Bootstrap of app first
 */
include __DIR__ . '/../configuration/bootstrap.php';

// Set up dependencies
require __DIR__ . '/../configuration/dependencies.php';

// Register middleware
require __DIR__ . '/../configuration/middleware.php';

// Register routes
require __DIR__ . '/../configuration/routes.php';

// Run app
$app->run();
