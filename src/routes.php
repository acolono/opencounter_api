<?php
// Routes
$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("OpenCounter '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});



/**
 * https://github.com/zircote/swagger-php#usage-from-php
 * @SWG\Swagger(
 *     basePath="/api/v1",
 *     host="api.opencounter.docker",
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

$app->get('/api/v1/docs', function($request, $response, $args) {
  $this->logger->info('gettin swagger');
  $swagger = \Swagger\scan(['../src']);
  header('Content-Type: application/json');
  return $response->withJson($swagger);
});


// Counter Routes


/**
 * routes that go directly to /counters with no additional path parameters
 */

$app->post('/api/v1/counters', '\OpenCounter\Api\CounterController:addCounter');


/**
 * Routes with additional path parameters
 *
 * return full counter objects in their responses if successful.
 */


$app->get('/api/v1/counters/{name}', '\OpenCounter\Api\CounterController:getCounter');

$app->post('/api/v1/counters/{name}', '\OpenCounter\Api\CounterController:newCounter');

$app->patch('/api/v1/counters/{name}/status', '\OpenCounter\Api\CounterController:setCounterStatus');

$app->patch('/api/v1/counters/{name}/value', '\OpenCounter\Api\CounterController:incrementCounter');

$app->put('/api/v1/counters/{name}/{password}', '\OpenCounter\Api\CounterController:setCounter');

/**
 * Some routes that provide limited responses
 */


$app->get('/api/v1/counters/{name}/value', '\OpenCounter\Api\CounterController:getCount');


