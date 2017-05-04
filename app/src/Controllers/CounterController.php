<?php

namespace SlimCounter\Controllers;

use OpenCounter\Application\Command\Counter\CounterAddCommand;
use OpenCounter\Application\Command\Counter\CounterIncrementValueCommand;
use OpenCounter\Application\Command\Counter\CounterRemoveCommand;
use OpenCounter\Application\Command\Counter\CounterResetValueCommand;
use OpenCounter\Application\Command\Counter\CounterSetStatusCommand;
use OpenCounter\Application\Query\Counter\CounterOfIdQuery;
use OpenCounter\Application\Service\Counter\CounterAddService;
use OpenCounter\Application\Service\Counter\CounterBuildService;
use OpenCounter\Application\Service\Counter\CounterIncrementValueService;
use OpenCounter\Application\Service\Counter\CounterRemoveService;
use OpenCounter\Application\Service\Counter\CounterResetValueService;
use OpenCounter\Application\Service\Counter\CounterSetStatusService;
use OpenCounter\Application\Service\Counter\CounterViewService;
use OpenCounter\Domain\Repository\CounterRepository;
use OpenCounter\Infrastructure\Persistence\StorageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Class CounterController.
 *
 * @package SlimCounter\Api
 */
class CounterController
{

    /**
     * A Logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * CounterRepository.
     *
     * @var \OpenCounter\Domain\Repository\CounterRepository
     */
    private $counter_repository;

    /**
     * CounterBuildService.
     *
     * @var \OpenCounter\Http\CounterBuildService
     */
    private $counterBuildService;

    /**
     * CounterRemoveService.
     *
     * @var \OpenCounter\Application\Service\Counter\CounterRemoveService
     */
    private $CounterRemoveService;

    /**
     * CounterAddService.
     *
     * @var \OpenCounter\Application\Service\Counter\CounterAddService
     */
    private $CounterAddService;

    /**
     * CounterIncrementValueService.
     *
     * @var \OpenCounter\Application\Service\Counter\CounterIncrementValueService
     */
    private $CounterIncrementValueService;

    /**
     * CounterViewService.
     *
     * @var \OpenCounter\Application\Service\Counter\CounterViewService
     */
    private $CounterViewService;

    /**
     * CounterResetValueService.
     *
     * @var \OpenCounter\Application\Service\Counter\CounterResetValueService
     */
    private $CounterResetValueService;

    /**
     * CounterSetStatusService.
     *
     * @var \OpenCounter\Application\Service\Counter\CounterSetStatusService
     */
    private $CounterSetStatusService;

    /**
     * Counter Controller Constructor.
     *
     * @param \Psr\Log\LoggerInterface                                              $logger
     * @param \OpenCounter\Application\Service\Counter\CounterBuildService          $counterBuildService
     * @param \OpenCounter\Infrastructure\Persistence\StorageInterface              $counter_mapper
     * @param \OpenCounter\Domain\Repository\CounterRepository                      $counter_repository
     * @param \OpenCounter\Application\Service\Counter\CounterAddService            $CounterAddService
     * @param \OpenCounter\Application\Service\Counter\CounterRemoveService         $CounterRemoveService
     * @param \OpenCounter\Application\Service\Counter\CounterIncrementValueService $CounterIncrementValueService
     * @param \OpenCounter\Application\Service\Counter\CounterViewService           $CounterViewService
     * @param \OpenCounter\Application\Service\Counter\CounterSetStatusService      $CounterSetStatusService
     * @param \OpenCounter\Application\Service\Counter\CounterResetValueService     $CounterResetValueService
     */
    public function __construct(
        LoggerInterface $logger,
        CounterBuildService $counterBuildService,
        StorageInterface $counter_mapper,
        CounterRepository $counter_repository,
        CounterAddService $CounterAddService,
        CounterRemoveService $CounterRemoveService,
        CounterIncrementValueService $CounterIncrementValueService,
        CounterViewService $CounterViewService,
        CounterSetStatusService $CounterSetStatusService,
        CounterResetValueService $CounterResetValueService
    ) {

        $this->logger = $logger;
        $this->counterBuildService = $counterBuildService;
        $this->SqlManager = $counter_mapper;
        $this->counter_repository = $counter_repository;
        $this->CounterAddService = $CounterAddService;
        $this->CounterRemoveService = $CounterRemoveService;
        $this->CounterIncrementValueService = $CounterIncrementValueService;
        $this->CounterViewService = $CounterViewService;
        $this->CounterSetStatusService = $CounterSetStatusService;
        $this->CounterResetValueService = $CounterResetValueService;
    }

    /**
     * Creating new counter.
     *
     * @SWG\Post(
     *   path = "/counters/{id}",
     *   operationId = "newCounter",
     *   description = "Creates a new Counter with name. Duplicates are
     *     allowed", summary = "create a new counter", produces
     *     ={"application/json"}, tags ={"docs"}, produces
     *     ={"application/json"}, security ={{
     *     "counter_auth": {"write:counters", "read:counters"},
     *   }},
     * @SWG\Parameter(
     *      parameter="id",
     *      description="id of counter to create",
     *      in="path",
     *      name="id",
     *      required=false,
     *      type="string",
     *      default="1ff4debe-6160-4201-93d1-568d5a50a886",
     * @SWG\Schema(ref = "#/definitions/CounterAddCommand")
     *     ),
     * @SWG\Parameter(
     *   name = "counter",
     *   in = "body",
     *   description = "Counter to add",
     *   required = true,
     * @SWG\Schema(ref = "#/definitions/counterInput"),
     *   ),
     * @SWG\Response(
     *   response = 200,
     *   description = "counter response",
     * @SWG\Schema(ref = "#/definitions/Counter")
     *   ),
     * @SWG\Response(
     *   response = "default",
     *   description = "unexpected error",
     * @SWG\Schema(ref = "#/definitions/errorModel")
     *   ),
     * @SWG\Response(
     *   response = "409",
     *   description = "Counter name is taken error",
     * @SWG\Schema(ref = "#/definitions/AlreadyExistsErrorModel")
     *   )
     *
     * )
     *
     *
     *
     *
     * this input definition needs to go somewhere else. e.g the command
     *     interface
     *
     * @SWG\Definition(
     *     definition = "counterInput",
     *     allOf ={
     * @SWG\Schema(
     * @SWG\Property(
     *           property = "value",
     *           type = "integer",
     *           format = "int64"
     *         ),
     * @SWG\Property(
     *           property = "name",
     *           type = "string"
     *         ),
     * @SWG\Property(
     *           property = "status",
     *           type = "string",
     *           default="active"
     *         )
     *       )
     *     }
     *   )
     *
     * @param $request
     * @param $response
     * @param $args
     *
     * @return mixed
     */
    public function addCounter(
        ServerRequestInterface $request,
        ResponseInterface $response,
        $args
    ) {
        // Assume everything will fail.
        $code = 400;
        try {
            // Get at data we need from server request object to build our request.
            $data = $request->getParsedBody();

            $name = $data['name'];
            $value = $data['value'];
            $status = 'active';
            $password = 'passwordplaceholder';
            $id = (isset($args['id'])) ? $args['id'] : null;

            // Call Service that executes appropriate command with given parameters.
            $result = $this->CounterAddService->execute(
                new CounterAddCommand(
                    $name,
                    $value,
                    $status,
                    $password,
                    $id
                )
            );

            $code = 201;
        } catch (\Exception $e) {
            $result = $e->getMessage();
            $code = 409;
            $this->logger->info('exception ' . $e->getMessage());
        }
        // Note that even though the response is json encoded if requested as
        // html you will get content type html showing the json.
        $response->write(json_encode($result));

        return $response->withStatus($code);
    }

    /**
     * Change Counter value Route.
     *
     * IncrementCounter.
     *
     * try to increment a counter. will fail if counter is locked or not found.
     * if successful returns updated counter object in response
     * counter name passed as part of args array
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param                                          $args
     *
     * @return \Psr\Http\Message\ResponseInterface      $response
     *
     * @SWG\Patch(
     *     path="/counters/value/{id}",
     *     tags={"docs"},
     *     operationId="incrementCounter",
     *     summary="increment existing counter",
     *     description="increments counter value",
     *     consumes={"application/json", "application/xml"},
     *     produces={"application/xml", "application/json"},
     * @SWG\Parameter(
     *      parameter="id",
     *      description="id of counter to increment",
     *      in="path",
     *      name="id",
     *      required=false,
     *      type="string",
     *      default="1ff4debe-6160-4201-93d1-568d5a50a886",
     * @SWG\Schema(ref = "#/definitions/CounterIncrementValueCommand")
     *     ),
     * @SWG\Parameter(
     *       name="increment",
     *       description="increment to change by",
     *       in="body",
     * @SWG\Schema(ref="#/definitions/CounterValue"),
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Invalid ID supplied",
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Counter not found",
     *     ),
     * @SWG\Response(
     *         response=405,
     *         description="Validation exception",
     *     ),
     * @SWG\Response(
     *         response=200,
     *         description="counter response",
     * @SWG\Schema(ref="#/definitions/Counter")
     *     ),
     *   security={{
     *     "api_key":{},
     *         "counter_auth": {"write:counters", "read:counters"},
     *   }}
     * )
     */
    public function incrementCounter(
        ServerRequestInterface $request,
        ResponseInterface $response,
        $args
    ) {

        // We assume everything is going to fail.
        $result = 'an error has occurred';
        $code = 400;

        try {
            $data = $request->getParsedBody();

            $increment = $data['value'];

            $this->CounterIncrementValueService->execute(
                new CounterIncrementValueCommand(
                    $args['id'],
                    $increment
                )
            );

            $code = 201;
        } catch (\Exception $e) {
            $result = $e->getMessage();
            // TODO: get the return code from the exception?
            $code = 409;
        }

        $body = $response->getBody();
        $body->write(json_encode($result));

        return $response->withStatus($code);
    }

    /**
     * Route for changing counter state.
     *
     * @SWG\Patch(
     *     path="/counters/status/{id}",
     *     tags={"docs"},
     *     operationId="setCounterStatus",
     *     summary="lock or unlock existing counter",
     *     description="sets counter status to active or locked",
     *     consumes={"application/json", "application/xml"},
     *     produces={"application/xml", "application/json"},
     * @SWG\Parameter(
     *      parameter="id",
     *      description="id of counter to lock or unlock",
     *      in="path",
     *      name="id",
     *      required=false,
     *      type="string",
     *      default="1ff4debe-6160-4201-93d1-568d5a50a886",
     * @SWG\Schema(ref = "#/definitions/CounterSetStatusCommand")
     *     ),
     * @SWG\Parameter(
     *       name="status",
     *       description="status to change to",
     *       type="string",
     *       in="body",
     *       default="locked",
     * @SWG\Schema(ref="#/definitions/CounterSetStatusCommand")
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Invalid ID supplied",
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Counter not found",
     *     ),
     * @SWG\Response(
     *         response=405,
     *         description="Validation exception",
     *     ),
     * @SWG\Response(
     *         response=201,
     *         description="counter response",
     * @SWG\Schema(ref="#/definitions/Counter")
     *     ),
     *   security={{
     *     "api_key":{},
     *         "counter_auth": {"write:counters", "read:counters"},
     *   }}
     * )
     * setCounterStatus
     *
     * counter name from args, set counter status from request body
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param                                          $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function setCounterStatus(
        ServerRequestInterface $request,
        ResponseInterface $response,
        $args
    ) {
        // We assume everything is going to fail.
        $result = 'an error has occurred';
        $code = 400;
        try {
            $data = $request->getParsedBody();

            // $counterName = $data['name'];.
            $counterStatus = $data['status'];
            $this->CounterSetStatusService->execute(
                new CounterSetStatusCommand(
                    $args['id'],
                    $counterStatus
                )
            );

            $code = 201;
        } catch (\Exception $e) {
            $result = json_encode($e->getMessage());
            $code = 409;
            $this->logger->info('exception ' . $e->getMessage());
        }

        $body = $response->getBody();
        $body->write(json_encode($result, JSON_UNESCAPED_SLASHES));

        return $response->withStatus($code);
    }

    /**
     * Set Counter Route.
     *
     * @SWG\Put(
     *     path="/counters/{id}",
     *     tags={"docs"},
     *     operationId="setCounter",
     *     summary="Reset counter",
     *     description="Reset a counter's value to 0",
     *     consumes={"application/json", "application/xml"},
     *     produces={"application/xml", "application/json"},
     * @SWG\Parameter(
     *       name="id",
     *       description="counter id to reset",
     *       type="string",
     *       in="body",
     *       default="1ff4debe-6160-4201-93d1-568d5a50a886",
     * @SWG\Schema(ref="#/definitions/CounterResetValueCommand")
     *     ),
     * @SWG\Parameter(
     *      parameter="id",
     *      description="id of counter to lock or unlock",
     *      in="path",
     *      name="id",
     *      required=false,
     *      type="string",
     *      default="1ff4debe-6160-4201-93d1-568d5a50a886",
     * @SWG\Schema(ref = "#/definitions/CounterResetValueCommand")
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Invalid ID supplied",
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Counter not found",
     *     ),
     * @SWG\Response(
     *         response=405,
     *         description="Validation exception",
     *     ),
     * @SWG\Response(
     *         response=200,
     *         description="counter response",
     * @SWG\Schema(ref="#/definitions/Counter")
     *     ),
     *   security={{
     *     "api_key":{},
     *         "counter_auth": {"write:counters", "read:counters"},
     *   }}
     * )
     * setCounter
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param                                          $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function setCounter(
        ServerRequestInterface $request,
        ResponseInterface $response,
        $args
    ) {
        // We assume everything is going to fail.
        $return = ['message' => 'an error has occurred'];
        $code = 400;

        try {
            // Get at data we need from server request object
            // to build our command and query.
            $data = $request->getParsedBody();

            $this->CounterResetValueService->execute(
                new CounterResetValueCommand($args['id'])
            );
            $code = 201;
        } catch (\Exception $e) {
            $return = $e->getMessage();
            $code = 409;
        }

        $body = $response->getBody();
        // Now how can we allow slim response to write to body like this? and how to handle mimetypes.
        $body->write(json_encode($return, JSON_UNESCAPED_SLASHES));

        return $response;
    }

    /**
     * Get Value only Route.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param                                          $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @SWG\Get(
     *     path="/counters/value/{id}",
     *     tags={"docs"},
     *     description="Returns a Counters value if the user has access to the
     *     Counter", summary="read value from counter", operationId="getCount",
     * @SWG\Parameter(
     *      parameter="id",
     *      description="id of counter to get",
     *      in="path",
     *      name="id",
     *      required=false,
     *      type="string",
     *      default="1ff4debe-6160-4201-93d1-568d5a50a886",
     * @SWG\Schema(ref = "#/definitions/CounterOfIdQuery")
     *     ),
     *     produces={
     *         "application/json",
     *         "application/xml",
     *         "text/html",
     *         "text/xml"
     *     },
     * @SWG\Response(
     *         response=200,
     *         description="counter value response",
     * @SWG\Schema(ref="#/definitions/CounterValue")
     *     ),
     * @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     * @SWG\Schema(ref="#/definitions/errorModel")
     *     ),
     *   security={{
     *     "api_key":{},
     *         "counter_auth": {"write:counters", "read:counters"},
     *   }}
     * )
     */
    public function getCount(
        ServerRequestInterface $request,
        ResponseInterface $response,
        $args
    ) {
        // We assume everything is going to fail.
        $return = ['message' => 'an error has occurred'];
        $code = 400;
        try {
            $counter = $this->CounterViewService->execute(
                new CounterOfIdQuery($args['id'])
            );
            $result = $counter->getValue();
            $code = 201;
        } catch (\Exception $e) {
            $result = $e->getMessage();
            $code = 409;
        }

        $body = $response->getBody();
        // Slims request class gives some handy shortcuts.
        // but we want to know how to write to responses with the basic psr7 interface.
        $body->write(json_encode($result));

        return $response;
    }

    /**
     * Get Counter.
     *
     * @SWG\Get(
     *     path="/counters/{id}",
     *     tags={"docs"},
     *     operationId="getCounter",
     *     description="Returns a Counter by id if the user has access to the
     *     Counter", summary="get entire counter", produces={
     *         "application/json",
     *         "application/xml",
     *         "text/html",
     *         "text/xml"
     *     },
     * @SWG\Parameter(
     *      parameter="id",
     *      description="id of counter to Delete",
     *      in="path",
     *      name="id",
     *      required=false,
     *      type="string",
     *      default="1ff4debe-6160-4201-93d1-568d5a50a886",
     * @SWG\Schema(ref="#/definitions/CounterOfIdQuery")
     *     ),
     * @SWG\Response(
     *         response=200,
     *         description="counter response",
     * @SWG\Schema(ref="#/definitions/Counter")
     *     ),
     * @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     * @SWG\Schema(ref="#/definitions/errorModel")
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="counter not found error",
     * @SWG\Schema(ref="#/definitions/NotFoundErrorModel")
     *     ),
     *   security={{
     *     "api_key":{},
     *         "counter_auth": {"write:counters", "read:counters"},
     *   }}
     * )
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param                                          $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getCounter(
        ServerRequestInterface $request,
        ResponseInterface $response,
        $args
    ) {
        // We assume everything is going to fail.
        $return = ['message' => 'an error has occurred'];
        $code = 400;
        try {
            $counter = $this->CounterViewService->execute(
                new CounterOfIdQuery($args['id'])
            );
            $result = $counter->toArray();
            $code = 200;
        } catch (\Exception $e) {
            $result = $e->getMessage();
            $code = 409;
        }

        $response->write(json_encode($result));

        return $response->withStatus($code);
    }

    /**
     * Delete Counter Route.
     *
     * Supposedly the name of the counter to be deleted is in the request body.
     * so create a counter remove request with the name.
     * pass it to the counter removal service.
     * return a response with either an json formatted error.
     *
     * TODO:  (see content negotiation on how to serve e.g. xml instead)
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param                                          $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @SWG\Delete(
     *     path="/counters/{id}",
     *     tags={"docs"},
     *     operationId="deleteCounter",
     *     summary="Delete counter",
     *     description="delete a counter by id",
     *     consumes={"application/json", "application/xml"},
     *     produces={"application/xml", "application/json"},
     * @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="Counter to be removed",
     *         required=true,
     * @SWG\Schema(ref="#/definitions/CounterRemoveCommand")
     *     ),
     * @SWG\Parameter(
     *      parameter="id",
     *      description="id of counter to Delete",
     *      in="path",
     *      name="id",
     *      required=false,
     *      type="string",
     *      default="1ff4debe-6160-4201-93d1-568d5a50a886",
     * @SWG\Schema(ref="#/definitions/CounterRemoveCommand")
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Invalid ID supplied",
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Counter not found",
     *     ),
     * @SWG\Response(
     *         response=405,
     *         description="Validation exception",
     *     ),
     * @SWG\Response(
     *         response=200,
     *         description="counter deleted",
     *     ),
     *   security={{
     *     "api_key":{},
     *         "counter_auth": {"write:counters", "read:counters"},
     *   }}
     * )
     */
    public function deleteCounter(
        ServerRequestInterface $request,
        ResponseInterface $response,
        $args
    ) {
        // We assume everything is going to fail.
        $return = ['message' => 'an error has occurred'];
        $code = 400;
        try {
            $this->CounterRemoveService->execute(new CounterRemoveCommand($args['id']));
            $code = 201;
            $result = 'received remove command';
        } catch (\Exception $e) {
            $code = 400;

            $result = $e->getMessage();
        }

        $body = $response->getBody();
        $body->write(json_encode($result, JSON_UNESCAPED_SLASHES));

        return $response;
    }

    /**
     * AllAction.
     *
     * @return array
     */
    public function allAction()
    {
        $counters = $this->get('counter_repository')->findAll();

        return ['counter' => $counters];
    }
}
