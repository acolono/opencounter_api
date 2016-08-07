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
    ],
    // Monolog settings
    'logger' => [
      'name' => 'slim-app',
      'path' => '/var/www/html/opencounter/logs/app.log',
    ],
    'db' =>
      [
        'host' => 'database',
        'user' => 'root',
        'pass' => 'countapp',
        'dbname' => 'countapp',
      ],
  ],
];
