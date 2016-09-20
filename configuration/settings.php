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
      'path' => '/var/www/opencounter-slim-codenv/logs/app.log',
    ],
    'db' =>
      [
        'host' =>  $production ? "localhost" : 'opencounter-slim-codenv-mysql',
        'dbname'  => $production ? "produciton_db" : "development_db",
        'user'  => $production ? "countapp" : "docker",
        'pass'  => $production ? "similarly-secure-password" : "docker",
      ],
  ],
];
