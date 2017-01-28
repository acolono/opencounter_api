<?php
$mode = (string) getenv('MODE');
return [
  'settings' => [
    'displayErrorDetails' => (bool) getenv('DISPLAY_ERRORS'),
    // set to false in production
    'addContentLengthHeader' => FALSE,
    // Allow the web server to send the content-length header

    // Renderer settings
    'renderer' => [
      'template_path' => APP_ROOT . '/templates/',
      'cache_path' => APP_ROOT . '/cache/',

    ],
    // Monolog settings
    'logger' => [
      'name' => 'slim-app',
      'level' => (int)getenv('LOG_LEVEL') ?: 400,
      'path' => 'slimcounter.log',
      'logger_path' => APP_ROOT . '/logs/',
    ],
    'db' =>
      [
        'host' => (string) getenv('DB_HOST'),
        'dbname'  => (string) getenv('MYSQL_DATABASE'),
        'user'  => (string) getenv('MYSQL_USER'),
        'pass'  => (string) getenv('MYSQL_PASSWORD'),
      ],
  ],
];
