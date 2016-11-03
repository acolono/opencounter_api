<?php
$production = (bool)getenv('PRODUCTION');

return [
  'settings' => [
    'displayErrorDetails' => (bool)getenv('DISPLAY_ERRORS'),
    // set to false in production
    'addContentLengthHeader' => FALSE,
    // Allow the web server to send the content-length header

    // Renderer settings
    'renderer' => [
      'template_path' => __DIR__ . '/../templates/',
      'cache_path' => __DIR__ . '/../cache/',
    ],
    // Monolog settings
    'logger' => [
      'name' => 'slim-app',
      'level' => (int)getenv('LOG_LEVEL') ?: 400,
      'path' => 'slimcounter.log',
    ],
    'db' =>
      [
        'host' =>  $production ? "localhost" : 'opencounter-slim-codenv-mysql',
        'dbname'  => $production ? "production_db" : "testing_db",
        'user'  => $production ? "countapp" : "testing",
        'pass'  => $production ? "similarly-secure-password" : "testing",
      ],
  ],
];
