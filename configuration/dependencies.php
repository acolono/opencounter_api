<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($container) {
    $settings = $container->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($container) {
    $settings = $container->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\RotatingFileHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};
$container['db'] = function($container) {
  $db = $container->get('settings')['db'];
  $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  return $pdo;
};

$container['counter_mapper'] = function($container) {
  $counter_mapper = new \OpenCounter\Infrastructure\Persistence\Sql\SqlManager($container->get('db'));
  return $counter_mapper;
};
$container['counter_repository'] = function($container) {
  $counter_mapper = $container->get('counter_mapper');
  $counter_repository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlPersistentCounterRepository($counter_mapper);
  return $counter_repository;
};


$container['counter_build_service'] = function($container) {
  $factory = new \OpenCounter\Infrastructure\Factory\CounterFactory();

  $counter_build_service = new \OpenCounter\Http\CounterBuildService($container->get('counter_repository'), $factory, $container['logger']);
  return $counter_build_service;
};
