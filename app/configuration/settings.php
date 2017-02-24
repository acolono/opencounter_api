<?php

if (!defined('APP_ROOT')) {
    $spl = new SplFileInfo(__DIR__ . '/..');
    define("APP_ROOT", $spl->getRealPath());
}

return [
  'settings' => [
    'displayErrorDetails' => (bool)getenv('DISPLAY_ERRORS'),
      // set to false in production
    'addContentLengthHeader' => false,

      // Renderer settings
    'renderer' => [
      'theme_path' => APP_ROOT . '/themes/default_theme/',
      'cache_path' => APP_ROOT . '/cache/',

    ],
      // Monolog settings
    'logger' => [
      'name' => 'slim-app',
      'level' => (int)getenv('LOG_LEVEL') ?: 400,
      'logger_path' => APP_ROOT . '/logs/slimcounter.log',
    ],
    'db' =>
      [
        'host' => (string)getenv('DB_HOST'),
        'dbname' => (string)getenv('MYSQL_DATABASE'),
        'user' => (string)getenv('MYSQL_USER'),
        'pass' => (string)getenv('MYSQL_PASSWORD'),
      ],
  ],
];
