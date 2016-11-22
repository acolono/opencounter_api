<?php

/**
 * @file
 * The dependencies system.
 *
 * Adding services to the container so we can pull them out when we need them.
 */

$container = $app->getContainer();

/**
 * Renderer used to display html pages loaded into container.
 *
 * @param $container
 *
 * @return \Slim\Views\PhpRenderer
 */
$container['renderer'] = function ($container) {
  $settings = $container->get('settings')['renderer'];
  $renderer = new \Slim\Views\Twig($settings['template_path'], [
//    'cache' => $settings['cache_path']
  ]);
  $renderer->addExtension(new \Slim\Views\TwigExtension(
    $container['router'],
    $container['request']->getUri()
  ));

  return $renderer;
};

/**
 * A logger could come in handy.
 *
 * @param $container
 *
 * @return \Monolog\Logger
 */
$container['logger'] = function ($container) {
    $settings = $container->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\RotatingFileHandler(__DIR__ . '/logs/' . $settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

/**
 * a PDO object connects us to a persistent database
 *
 * @param $container
 *
 * @return \PDO
 */
$container['db'] = function ($container) {
  $db = $container->get('settings')['db'];
  $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  return $pdo;
};

/**
 * A way to access the db as pdo object
 *
 * @param $container
 *
 * @return \OpenCounter\Infrastructure\Persistence\Sql\SqlManager
 */
$container['counter_mapper'] = function ($container) {
  $counter_mapper = new \OpenCounter\Infrastructure\Persistence\Sql\SqlManager($container->get('db'));
  return $counter_mapper;
};

/**
 * Using SQL storage through repository interface
 *
 * @param $container
 *
 * @return \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlPersistentCounterRepository
 */
$container['counter_repository'] = function ($container) {
  $counter_mapper = $container->get('counter_mapper');
  $counter_repository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlPersistentCounterRepository($counter_mapper);
  return $counter_repository;
};


/**
 * A service that should be used to create new Counter objects.
 *
 * @param $container
 *
 * @return \OpenCounter\Http\CounterBuildService
 */
$container['counter_build_service'] = function ($container) {
  $factory = new \OpenCounter\Infrastructure\Factory\Counter\CounterFactory();

  $counter_build_service = new \OpenCounter\Http\CounterBuildService($container->get('counter_repository'), $factory, $container['logger']);
  return $counter_build_service;
};
// explicitly add controller to container so its not constructed with container as first argument cause we dont actullay want to pass the container to the controller
$container['\OpenCounter\Http\CounterController'] = function ($container) {
  $counter_controller = new \OpenCounter\Http\CounterController(
      $container['logger'],
      $container['counter_build_service'],
      $container['counter_mapper'],
      $container['counter_repository']
  );
  return $counter_controller;
};