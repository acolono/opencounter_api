<?php
return [
  'settings' => [
    'displayErrorDetails' => TRUE,
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
      'path' => '/var/www/opencounter-slim-codenv/logs/app.log',
    ],
    'db' =>
      [
        'host' => 'opencounter-slim-codenv-mysql',
        'user' => 'docker',
        'pass' => 'docker',
        'dbname' => 'development_db',
      ],
  ],
];
