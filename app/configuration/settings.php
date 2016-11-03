<?php
$mode = (string)getenv('MODE');
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
        'host' => ($mode == 'production') ? "localhost" : 'opencounter-slim-codenv-mysql',
        'dbname'  => ($mode == 'production') ? "production_db" : ($mode == 'testing') ? "testing_db" : "development_db",
        'user'  => ($mode == 'production') ? "countapp" : ($mode == 'testing') ? "$mode" : "docker",
        'pass'  => ($mode == 'production') ? "similarly-secure-password" : ($mode == 'testing') ? "testing" : "docker",
      ],
  ],
];
