<?php

/**
 * The dependencies system.
 *
 * Adding services to the container so we can pull them out when we need them.
 * for now this includes application services but those should move to another file eventually
 *
 * @file
 */


$container = $app->getContainer();

/**
 * a PDO object connects us to a persistent database
 *
 * we are reading db credentials from settings,
 * creating a pdo object which we make accessible through the container.
 *
 * @param $container
 *
 * @return \PDO
 */
$container['pdo'] = function ($container) {
    $db = $container->get('settings')['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
      $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $pdo;
};

//
///**
// * For dealing with events in the system
// *
// * @param $container
// * @return \SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware
// */
//$container['commandBus'] = function ($container) {
//    $commandBus = new \SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware();
//    $commandBus->appendMiddleware(new \SimpleBus\Message\Bus\Middleware\FinishesHandlingMessageBeforeHandlingNext());
//    $commandNameResolver = new \SimpleBus\Message\Name\ClassBasedNameResolver();
//
//// Provide a map of command names to callables. You can provide actual callables, or lazy-loading ones.
//    $commandHandlersByCommandName = [
//      '\BenGorUser\User\Application\Command\LogIn\LogInUserCommand' => ['user_login_application_service', 'handle'],
//      '\BenGorUser\User\Application\Command\SignUp\SignUpUserCommand' => ['user_signup_application_service', 'handle'],
//
//    ];
//
//    $commandHandlerMap = new SimpleBus\Message\CallableResolver\CallableMap($commandHandlersByCommandName,
//      \Slim\CallableResolver::class);
//
//    $commandHandlerResolver = new \SimpleBus\Message\Handler\Resolver\NameBasedMessageHandlerResolver(
//      $commandNameResolver,
//      $commandHandlerMap
//    );
//
//    $commandBus->appendMiddleware(
//      new \SimpleBus\Message\Handler\DelegatesToMessageHandlerMiddleware(
//        $commandHandlerResolver
//      )
//    );
//    return $commandBus;
//};
//
//$container['eventBus'] = function ($container) {
//    $eventBus = new \SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware();
//    $eventBus->appendMiddleware(new \SimpleBus\Message\Bus\Middleware\FinishesHandlingMessageBeforeHandlingNext());
//
//    $eventNameResolver = new \SimpleBus\Message\Name\ClassBasedNameResolver();
//// Provide a map of command names to callables. You can provide actual callables, or lazy-loading ones.
//    $eventSubscribersByEventName = [
////        Fully\Qualified\Class\Name\Of\Event::class => [ // an array of "callables",
//
//
//    ];
//
//    $eventSubscriberCollection = new \SimpleBus\Message\CallableResolver\CallableCollection(
//        $eventSubscribersByEventName,
//    // can we use the container interop instead of sesrvicelocator
//        new \SimpleBus\Message\CallableResolver\ServiceLocatorAwareCallableResolver($container)
//    );
//    $eventSubscribersResolver = new NameBasedMessageSubscriberResolver(
//        $eventNameResolver,
//        $eventSubscriberCollection
//    );
//
//    $commandBus->appendMiddleware(
//        new \SimpleBus\Message\Handler\DelegatesToMessageHandlerMiddleware(
//            $commandHandlerResolver
//        )
//    );
//    return $commandBus;
//};
//
//
////
//$container['UserCommandBus'] = function ($container) {
//
//    $UserCommandBus = new \BenGorUser\SimpleBusBridge\CommandBus\SimpleBusUserCommandBus($container->get('commandBus'));
//    return $UserCommandBus;
//};
////
//$container['UserEventBus'] = function ($container) {
//
//    $UserEventBus = new \BenGorUser\SimpleBusBridge\EventBus\SimpleBusUserEventBus($container->get('commandBus'));
//    return $UserEventBus;
//};

/**
 * Renderer used to display html pages loaded into container.
 *
 * @param $container
 *
 */

// this file was getting too long, so experimentally split out some service definitions out into a seperate file (service providers below inject them into the container.)
$container->register(new \SlimCounter\Infrastructure\ServiceProvider\Oauth2ServiceProvider());

$container->register(new \SlimCounter\Infrastructure\ServiceProvider\SlimCounterServiceProvider());
//$container->register(new \SlimCounter\Infrastructure\ServiceProvider\UserServiceProvider());

/**
 * Counter Controller
 *
 * explicitly add controller to container so its not constructed
 * with container as first argument cause we dont actullay want
 * to pass the container to the controller.
 * instead we explicitly load required services from container into constructor.
 *
 * @param $container
 * @return \SlimCounter\Controllers\CounterController
 */
$container['\SlimCounter\Controllers\CounterController'] = function ($container
) {

    $CounterController = new \SlimCounter\Controllers\CounterController(
      $container['logger'],
      $container['counter_build_service'],
      $container['counter_mapper'],
      $container['counter_repository'],
      $container['CounterAddService'],
      $container['CounterRemoveService'],
      $container['CounterIncrementValueService'],
      $container['CounterViewService'],
      $container['CounterSetStatusService'],
      $container['CounterResetValueService']

    );

    return $CounterController;
};

// slims useful twig view implementation.
$container['renderer'] = function ($container) {
    $settings = $container->get('settings')['renderer'];

    $renderer = new \Slim\Views\Twig($settings['theme_path'] . 'templates', [
//    'cache' => $settings['cache_path']
        // TODO: debug only according to single setting
      'debug' => true
    ]);
    $renderer->getLoader()
      ->addPath($settings['theme_path'] . 'source/_layouts');
    $renderer->getLoader()
      ->addPath($settings['theme_path'] . 'source/_patterns', 'patterns');
    $renderer->getLoader()
      ->addPath($settings['theme_path'] . 'source/_patterns/02-elements',
        'elements');
    $renderer->getLoader()
      ->addPath($settings['theme_path'] . 'source/_patterns/00-atoms', 'atoms');

    $renderer->addExtension(new \Slim\Views\TwigExtension(
      $container['router'],
      $container['request']->getUri()
    ));
    $renderer->addExtension(new Twig_Extension_Debug());

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
    $logger->pushProcessor(new Monolog\Processor\WebProcessor());
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushProcessor(new \Monolog\Processor\IntrospectionProcessor($settings['level']));

    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['logger_path'],
      Monolog\Logger::DEBUG));

    return $logger;
};
