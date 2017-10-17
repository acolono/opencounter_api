<?php
/**
 * The routes registry.
 *
 * creating named routes and mapping them to controllers.
 *
 * @file
 */

use Chadicus\Slim\OAuth2\Routes;

// setup container
$container = $app->getContainer();

// TODO: swagger annotate oauth routes explicitly so people know what to post where
// e.g. opencounter-slim-codenv-webserver:8080/authorize?response_type=token&realm=1&user_id=librarian&client_id=librarian&scope=read%3Acounters write%3Acounters&state=counter_auth
// redirect url and user id need explaining http://172.25.0.5/o2c.html
$app->map([
  'GET',
  'POST'
], Routes\Authorize::ROUTE, new Routes\Authorize($container['oauth2_server'],
  $container['authorization_views']))
  ->setName('authorize');

$app->post(Routes\Token::ROUTE,
  new Routes\Token($container->get('oauth2_server')))->setName('token');

$app->map([
  'GET',
  'POST'
], Routes\ReceiveCode::ROUTE,
  new Routes\ReceiveCode($container->get('authorization_views')))
  ->setName('receive-code');

$app->post(Routes\Revoke::ROUTE,
  new Routes\Revoke($container->get('oauth2_server')))->setName('revoke');

/**
 * Admin Routes
 *
 * protected via oauth
 */

$app->group('/admin', function () {
    $this->get('/',
      '\SlimCounter\Controllers\AdminUiController:index')
      ->setName('admin.index');
    // list of users
    $this->get('/clients',
      '\SlimCounter\Controllers\UsersController:clientsIndex')
      ->setName('admin.client.index');

    $this->get('/clients/add',
      '\SlimCounter\Controllers\UsersController:addClientForm')
      ->setName('admin.client.add');
    // receives posts from  add oauth2_client form
    $this->post('/clients/new',
      '\SlimCounter\Controllers\UsersController:newClient')
      ->setName('admin.client.new');

    // Get admin overview over counters
    $this->get('/counters',
      '\SlimCounter\Controllers\AdminUiController:countersIndex')
      ->setName('admin.counter.index');
    // get new counter form
    $this->get('/content/add',
      '\SlimCounter\Controllers\AdminUiController:newCounterForm')
      ->setName('admin.counter.new');
    // view a specific counter
    $this->get('/counters/{name}',
      '\SlimCounter\Controllers\AdminUiController:viewCounter')
      ->setName('admin.counter.view');
    // Add Counter Route for admins is called by submitting New Counter Form
    $this->post('/content/add/counter',
      '\SlimCounter\Controllers\AdminUiController:addCounter')
      ->setName('admin.counter.add');
});

/**
 * Expose generated swagger.json (v2) on a route.
 *
 * special attention is paid to swg info tag where we are versioning our api
 *
 * we are using annotations to generate the documentation of our rest api
 * @see https://github.com/zircote/swagger-php#usage-from-php
 *
 * @SWG\Swagger(
 *   basePath="/api",
 *   host=API_HOST,
 *   schemes={"http"},
 *   produces={"application/json"},
 *   consumes={"application/json"},
 *   @SWG\Info(
 *     version="1.1.3",
 *     title="Swagger Open counter",
 *     description="A sample API that uses a counter as an example to demonstrate practices patterns and principles",
 *     termsOfService="http://acolono.com/terms/",
 *     @SWG\Contact(name="Acolono API Team"),
 *     @SWG\License(name="MIT")
 *   ),
 *   @SWG\SecurityScheme(
 *     securityDefinition="api_key",
 *     type="apiKey",
 *     in="header",
 *     name="api_key"
 *   ),
 *   @SWG\SecurityScheme(
 *     securityDefinition="counter_auth",
 *     type="oauth2",
 *     authorizationUrl="http://opencounter-slim-codenv-webserver:8080/authorize",
 *     flow="implicit",
 *     scopes={
 *       "read:counters": "read your counters",
 *       "write:counters": "modify counters in your account"
 *     }
 *   ),
 *   @SWG\Definition(
 *     definition="errorModel",
 *     required={"code", "message"},
 *     @SWG\Property(
 *       property="code",
 *       type="integer",
 *       format="int32"
 *     ),
 *     @SWG\Property(
 *       property="message",
 *       type="string"
 *     )
 *   )
 * )
 */

$app->get('/api', function ($request, $response, $args) {
    $this->logger->info('gettin swagger');
    $swagger = \Swagger\scan([
      '../configuration/',
      '../src/',
      '../vendor/rosenstrauch/opencounter_api_core/src/'
    ]);
    header('Content-Type: application/json');

    return $response->withJson($swagger);
});

$app->group('/api/counters', function () {



    /**
     * routes that go directly to /counters with optional id as additional path parameters
     */
    $this->get('/', '\SlimCounter\Controllers\CounterController:counterIndex')->add($this->getContainer()['authorization']->withRequiredScope(['read:counters']));

    $this->delete('/{id}',
      '\SlimCounter\Controllers\CounterController:deleteCounter')->add($this->getContainer()['authorization']->withRequiredScope(['write:counters read:counters']));

    $this->patch('/status[/{id}]',
      '\SlimCounter\Controllers\CounterController:setCounterStatus')->add($this->getContainer()['authorization']->withRequiredScope(['write:counters read:counters']));
    $this->patch('/value[/{id}]',
      '\SlimCounter\Controllers\CounterController:incrementCounter')->add($this->getContainer()['authorization']->withRequiredScope(['write:counters read:counters']));
    $this->get('/value[/{id}]',
      '\SlimCounter\Controllers\CounterController:getCount')->add($this->getContainer()['authorization']->withRequiredScope(['read:counters']));
    $this->get('/name[/{name}]',
      '\SlimCounter\Controllers\CounterController:getCounterByName')->add($this->getContainer()['authorization']->withRequiredScope(['read:counters']));

    $this->post('/[{id}]',
      '\SlimCounter\Controllers\CounterController:addCounter')->add($this->getContainer()['authorization']->withRequiredScope(['write:counters read:counters']));
    $this->get('/{id}',
      '\SlimCounter\Controllers\CounterController:getCounter')->add($this->getContainer()['authorization']->withRequiredScope(['read:counters']));
    $this->put('/[{id}]',
      '\SlimCounter\Controllers\CounterController:setCounter')->add($this->getContainer()['authorization']->withRequiredScope(['write:counters read:counters']));
    $this->patch('/[{id}]',
      '\SlimCounter\Controllers\CounterController:incrementCounter')->add($this->getContainer()['authorization']->withRequiredScope(['write:counters read:counters']));
});

// Fallback Route
//$app->get('/[{name}]', '\SlimCounter\Controllers\DefaultController:index');
