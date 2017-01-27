<?php
/**
 * @file
 * The routes registry.
 *
 * creating named routes and mapping them to controllers.
 */

use Chadicus\Slim\OAuth2\Routes;
use Slim\Views;
use OAuth2\Storage;
use Chadicus\Slim\OAuth2\Middleware;
use OAuth2\GrantType;

$storage = new Storage\Pdo($container['db']);

//Setup Auth
$server = new OAuth2\Server(
  $storage,
  [
//    'access_lifetime' => 3600,
    'allow_implicit' => TRUE,
  ],
  [
    new GrantType\ClientCredentials($storage),
//    new GrantType\UserCredentials($storage),
    new GrantType\AuthorizationCode($storage),
//    new GrantType\RefreshToken($storage),
  ]
);
$authorization = new Middleware\Authorization($server, $app->getContainer());

//Auth Routes

$auth_renderer = new Views\PhpRenderer(__DIR__ . '/../vendor/chadicus/slim-oauth2-routes/templates');

$app->map([
  'GET',
  'POST'
], Routes\Authorize::ROUTE, new Routes\Authorize($server, $auth_renderer))
  ->setName('authorize');
$app->post(Routes\Token::ROUTE, new Routes\Token($server))->setName('token');
$app->map([
  'GET',
  'POST'
], Routes\ReceiveCode::ROUTE, new Routes\ReceiveCode($auth_renderer))
  ->setName('receive-code');

// Admin Routes


$app->group('/admin', function () {
  // Get admin overview over counters
  $this->get('/counters',
    '\SlimCounter\Controllers\AdminUiController:index')
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
 * @SWG\Swagger(
 *     basePath="/api",
 *     host=API_HOST,
 *     schemes={"http"},
 *     produces={"application/json"},
 *     consumes={"application/json"},
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Swagger Open counter",
 *         description="A sample API that uses a counter as an example to demonstrate api principles",
 *         termsOfService="http://acolono.com/terms/",
 *         @SWG\Contact(name="Acolono API Team"),
 *         @SWG\License(name="MIT")
 *     ),
 *     @SWG\SecurityScheme(
 *      securityDefinition="api_key",
 *      type="apiKey",
 *      in="header",
 *      name="api_key"
 *     ),
 * @SWG\SecurityScheme(
 *   securityDefinition="counter_auth",
 *   type="oauth2",
 *   authorizationUrl="http://opencounter-slim-codenv-webserver:8080/authorize",
 *   flow="implicit",
 *   scopes={
 *     "read:counters": "read your counters",
 *     "write:counters": "modify counters in your account"
 *   }
 * ),
 *     @SWG\Definition(
 *         definition="errorModel",
 *         required={"code", "message"},
 *         @SWG\Property(
 *             property="code",
 *             type="integer",
 *             format="int32"
 *         ),
 *         @SWG\Property(
 *             property="message",
 *             type="string"
 *         )
 *     )
 * )
 */

$app->get('/api', function ($request, $response, $args) {
  $this->logger->info('gettin swagger');
  $swagger = \Swagger\scan([
    '../configuration/',
    '../vendor/rosenstrauch/opencounter_api_core/src/'
  ]);
  header('Content-Type: application/json');
  return $response->withJson($swagger);
});


$app->group('/api/counters', function () {


  /**
   * routes that go directly to /counters with no additional path parameters
   */

  $this->post('/', '\OpenCounter\Http\CounterController:addCounter');


  /**
   * Get Counter Route.
   *
   * @SWG\Get(
   *     path="/counters/{name}",
   *     tags={"docs"},
   *     operationId="getCounter",
   *     description="Returns a Counter if the user has access to the Counter",
   *     summary="get entire counter",
   *     @SWG\Parameter(ref="#/parameters/CounterName"),
   *     produces={
   *         "application/json",
   *         "application/xml",
   *         "text/html",
   *         "text/xml"
   *     },
   *     @SWG\Response(
   *         response=200,
   *         description="counter response",
   *         @SWG\Schema(ref="#/definitions/Counter")
   *     ),
   *     @SWG\Response(
   *         response="default",
   *         description="unexpected error",
   *         @SWG\Schema(ref="#/definitions/errorModel")
   *     ),
   *   security={{
   *     "api_key":{},
   *         "counter_auth": {"write:counters", "read:counters"},
   *   }}
   *   )
   */
  $this->get('/{name}', '\OpenCounter\Http\CounterController:getCounter');

  /**
   * Creating new counter.
   *
   * @param $request
   * @param $response
   * @param $args
   *
   * @return mixed
   *
   * @SWG\Post(
   *     path="/counters/{name}",
   *     tags={"docs"},
   *     operationId="newCounter",
   *     description="Creates a new Counter. Duplicates are allowed",
   *     summary="setup a new counter an existing counter",
   *     produces={"application/json"},
   *     @SWG\Parameter(ref="#/parameters/CounterName"),
   *     @SWG\Parameter(
   *         name="counter",
   *         in="body",
   *         description="Counter object to add",
   *         required=true,
   *         @SWG\Schema(ref="#/definitions/counterInput"),
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="counter response",
   *         @SWG\Schema(ref="#/definitions/Counter")
   *     ),
   *     @SWG\Response(
   *         response="default",
   *         description="unexpected error",
   *         @SWG\Schema(ref="#/definitions/errorModel")
   *     ),
   *   security={{
   *     "api_key":{},
   *         "counter_auth": {"write:counters", "read:counters"},
   *   }}
   *    )
   * @SWG\Definition(
   *     definition="counterInput",
   *     allOf={
   *         @SWG\Schema(
   *             @SWG\Property(
   *                 property="value",
   *                 type="integer",
   *                 format="int64"
   *             ),
   *             @SWG\Property(
   *                 property="name",
   *                 type="string"
   *             ),
   *              @SWG\Property(
   *                 property="status",
   *                 type="string",
   *                 default="active"
   *             )
   *         )
   *     }
   * )
   */
  $this->post('/{name}', '\OpenCounter\Http\CounterController:newCounter');

  /**
   * Route for changing counter state
   *
   * @param $request
   * @param $response
   * @param $args
   *
   * @return mixed
   *
   * @SWG\Patch(
   *     path="/counters/{name}/status",
   *     tags={"docs"},
   *     operationId="setCounterState",
   *     summary="lock or unlock existing counter",
   *     description="partially updates existing counter",
   *     consumes={"application/json", "application/xml"},
   *     produces={"application/xml", "application/json"},
   *     @SWG\Parameter(ref="#/parameters/CounterName"),
   *     @SWG\Parameter(
   *       name="status",
   *       description="status to change to",
   *       type="string",
   *       in="body",
   *       default="locked",
   *       @SWG\Schema(ref="#/definitions/Counter"),
   *     ),
   *     @SWG\Response(
   *         response=400,
   *         description="Invalid ID supplied",
   *     ),
   *     @SWG\Response(
   *         response=404,
   *         description="Counter not found",
   *     ),
   *     @SWG\Response(
   *         response=405,
   *         description="Validation exception",
   *     ),
   *     @SWG\Response(
   *         response=201,
   *         description="counter response",
   *         @SWG\Schema(ref="#/definitions/Counter")
   *     ),
   *   security={{
   *     "api_key":{},
   *         "counter_auth": {"write:counters", "read:counters"},
   *   }}
   * )
   */
  $this->patch('/{name}/status',
    '\OpenCounter\Http\CounterController:setCounterStatus');

  /**
   * Change Counter value Route.
   *
   * @SWG\Patch(
   *     path="/counters/{name}/value",
   *     tags={"docs"},
   *     operationId="inrementCounter",
   *     summary="increment existing counter",
   *     description="partially updates existing counter",
   *     consumes={"application/json", "application/xml"},
   *     produces={"application/xml", "application/json"},
   *     @SWG\Parameter(ref="#/parameters/CounterName"),
   *     @SWG\Parameter(
   *       name="increment",
   *       description="increment to change by",
   *       in="body",
   *       @SWG\Schema(ref="#/definitions/CounterValue"),
   *     ),
   *     @SWG\Response(
   *         response=400,
   *         description="Invalid ID supplied",
   *     ),
   *     @SWG\Response(
   *         response=404,
   *         description="Counter not found",
   *     ),
   *     @SWG\Response(
   *         response=405,
   *         description="Validation exception",
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="counter response",
   *         @SWG\Schema(ref="#/definitions/Counter")
   *     ),
   *   security={{
   *     "api_key":{},
   *         "counter_auth": {"write:counters", "read:counters"},
   *   }}
   * )
   */
  $this->patch('/{name}/value',
    '\OpenCounter\Http\CounterController:incrementCounter');

  /**
   * Delete Couter Route
   *
   * @SWG\Delete(
   *     path="/counters/{name}/{password}",
   *     tags={"docs"},
   *     operationId="deleteCounter",
   *     summary="Delete counter",
   *     description="",
   *     consumes={"application/json", "application/xml"},
   *     produces={"application/xml", "application/json"},
   *     @SWG\Parameter(
   *         name="body",
   *         in="body",
   *         description="Counter object that needs to be updated",
   *         required=true,
   *         @SWG\Schema(ref="#/definitions/counterInput"),
   *     ),
   *     @SWG\Parameter(
   *         name="password",
   *         in="path",
   *         description="Counter password to add",
   *         required=true,
   *         type="string",
   *     ),
   *     @SWG\Parameter(ref="#/parameters/CounterName"),
   *     @SWG\Response(
   *         response=400,
   *         description="Invalid ID supplied",
   *     ),
   *     @SWG\Response(
   *         response=404,
   *         description="Counter not found",
   *     ),
   *     @SWG\Response(
   *         response=405,
   *         description="Validation exception",
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="counter response",
   *         @SWG\Schema(ref="#/definitions/Counter")
   *     ),
   *   security={{
   *     "api_key":{},
   *         "counter_auth": {"write:counters", "read:counters"},
   *   }}
   * )
   */
  $this->delete('/{name}/{password}',
    '\OpenCounter\Http\CounterController:deleteCounter');

  /**
   * Set Couter Route
   *
   * @SWG\Put(
   *     path="/counters/{name}/{password}",
   *     tags={"docs"},
   *     operationId="setCounter",
   *     summary="Set counter",
   *     description="",
   *     consumes={"application/json", "application/xml"},
   *     produces={"application/xml", "application/json"},
   *     @SWG\Parameter(
   *         name="body",
   *         in="body",
   *         description="Counter object that needs to be updated",
   *         required=true,
   *         @SWG\Schema(ref="#/definitions/counterInput"),
   *     ),
   *     @SWG\Parameter(
   *         name="password",
   *         in="path",
   *         description="Counter password to add",
   *         required=true,
   *         type="string",
   *     ),
   *     @SWG\Parameter(ref="#/parameters/CounterName"),
   *     @SWG\Response(
   *         response=400,
   *         description="Invalid ID supplied",
   *     ),
   *     @SWG\Response(
   *         response=404,
   *         description="Counter not found",
   *     ),
   *     @SWG\Response(
   *         response=405,
   *         description="Validation exception",
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="counter response",
   *         @SWG\Schema(ref="#/definitions/Counter")
   *     ),
   *   security={{
   *     "api_key":{},
   *         "counter_auth": {"write:counters", "read:counters"},
   *   }}
   * )
   */
  $this->put('/{name}/{password}',
    '\OpenCounter\Http\CounterController:setCounter');

  /**
   * Some routes that provide limited responses
   */


  /**
   * Get Value only Route
   * @SWG\Get(
   *     path="/counters/{name}/value",
   *     tags={"docs"},
   *     description="Returns a Counters value if the user has access to the Counter",
   *     summary="read value from counter",
   *     operationId="getCount",
   *     @SWG\Parameter(ref="#/parameters/CounterName"),
   *     produces={
   *         "application/json",
   *         "application/xml",
   *         "text/html",
   *         "text/xml"
   *     },
   *     @SWG\Response(
   *         response=200,
   *         description="counter value response",
   *         @SWG\Schema(ref="#/definitions/CounterValue")
   *     ),
   *     @SWG\Response(
   *         response="default",
   *         description="unexpected error",
   *         @SWG\Schema(ref="#/definitions/errorModel")
   *     ),
   *   security={{
   *     "api_key":{},
   *         "counter_auth": {"write:counters", "read:counters"},
   *   }}
   * )
   */
  $this->get('/{name}/value', '\OpenCounter\Http\CounterController:getCount');

})->add($authorization->withRequiredScope(['write:counters read:counters']));

// Fallback Route
$app->get('/[{name}]', '\SlimCounter\Controllers\DefaultController:index');
