<?php

// Routes
$app->get('/admin/counters',
  '\OpenCounter\AdminUi\AdminUiController:index')->setName('admin.counter.index');
$app->get('/admin/counters/add',
  '\OpenCounter\AdminUi\AdminUiController:newCounter')->setName('admin.counter.add');
$app->get('/admin/counters/{name}',
  '\OpenCounter\AdminUi\AdminUiController:viewCounter')->setName('admin.counter.view');

$app->post('/admin/counters/{name}',
  '\OpenCounter\AdminUi\AdminUiController:addCounter')->setName('admin.counter.add');

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
  $swagger = \Swagger\scan(['../configuration/', '../src/OpenCounter']);
  header('Content-Type: application/json');
  return $response->withJson($swagger);
});


// Counter Routes


/**
 * routes that go directly to /counters with no additional path parameters
 */

$app->post('/api/v1/counters', '\OpenCounter\Http\CounterController:addCounter');



/**
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
 *     )
 * )
 */
$app->get('/api/v1/counters/{name}', '\OpenCounter\Http\CounterController:getCounter');

/**
 * Creating new counter.
 *
 * @param $request
 * @param $response
 * @param $args
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
 *     )
 * )
 * @SWG\Definition(
 *     definition="counterInput",
 *     allOf={
 *         @SWG\Schema(
 *             @SWG\Property(
 *                 property="value",
 *                 type="integer",
 *                 format="int64"
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
$app->post('/api/v1/counters/{name}', '\OpenCounter\Http\CounterController:newCounter');

/**
 * setCounter
 *
 * setCounter gets put requests from counter route.
 * /api/v1/counters/{name}/value
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
 *     security={{"opencounter_auth":{"write:counters", "read:counters"}}}
 * )
 */
$app->patch('/api/v1/counters/{name}/status', '\OpenCounter\Http\CounterController:setCounterStatus');

/**
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
 *     security={{"opencounter_auth":{"write:counters", "read:counters"}}}

 * )
 */
$app->patch('/api/v1/counters/{name}/value', '\OpenCounter\Http\CounterController:incrementCounter');

/**
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
 *     security={{"opencounter_auth":{"write:counters", "read:counters"}}}
 * )
 */
$app->put('/api/v1/counters/{name}/{password}', '\OpenCounter\Http\CounterController:setCounter');

/**
 * Some routes that provide limited responses
 */


/**
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
 *     )
 * )
 */
$app->get('/api/v1/counters/{name}/value', '\OpenCounter\Http\CounterController:getCount');


// Routes
$app->get('/[{name}]', '\OpenCounter\Http\DefaultController:index');
