<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 8/6/16
 * Time: 11:46 AM
 *
 * Contains Methods that receive requests, try to interact with counter objects
 * and counter repository and return a response.
 * this is achieved by individual application services we inject via the constructor
 * each method writes to log during every step. this is in the process of being replaced by domaineventaware logger.
 * validation happens in the counter objects where exceptions are thrown
 * exceptions are currently caught here in the controller and translated to appropriate error codes and error messages
 * the controller figures out what the request wants, pulls together everything and hands back an appropriate response.
 */

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
 * Class CounterController
 *
 * @package SlimCounter\Api
 */
class CounterController
{

    /**
     * A Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * CounterRepository
     *
     * @var \OpenCounter\Domain\Repository\CounterRepository
     */
    private $counter_repository;

    /**
     * @var \OpenCounter\Http\CounterBuildService
     */
    private $counterBuildService;

    /**
     * @var \OpenCounter\Application\Service\Counter\CounterRemoveService
     */
    private $CounterRemoveService;

    /**
     * @var \OpenCounter\Application\Service\Counter\CounterAddService
     */
    private $CounterAddService;

    /**
     * @var \OpenCounter\Application\Service\Counter\CounterIncrementValueService
     *
     */
    private $CounterIncrementValueService;
    /**
     * @var \OpenCounter\Application\Service\Counter\CounterViewService
     */
    private $CounterViewService;
    /**
     * @var \OpenCounter\Application\Service\Counter\CounterResetValueService
     */
    private $CounterResetValueService;
    /**
     * @var \OpenCounter\Application\Service\Counter\CounterSetStatusService
     */
    private $CounterSetStatusService;

    /**
     * CounterController constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \OpenCounter\Http\CounterBuildService $counterBuildService
     * @param \OpenCounter\Infrastructure\Persistence\StorageInterface $counter_mapper
     * @param \OpenCounter\Domain\Repository\CounterRepository $counter_repository
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
     * New Counter
     *
     * this method is meant to be called by the add counter route
     * with counter id as optional path argument
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param $args
     * @return \Psr\Http\Message\ResponseInterface|static
     */
//    public function newCounter(
//      RequestInterface $request,
//      ResponseInterface $response,
//      $args
//    ) {
//        // Assume everything will fail
//        $code = 400;
//        try {
//            // Get at data we need from server request object
//            // to build our command and query.
//            $data = $request->getParsedBody();
//
//            // TODO: when creating counters who will create the new id.
//            // how does the api client know the id
//            //$id = new CounterId($args['id']);
//            $name = $data['name'];
//            $value = $data['value'];
//            $status = 'active';
//            $password = 'passwordplaceholder';
//
//            // Call Service that executes appropriate
//            // command with given parameters.
//
//            $this->CounterAddService->execute(
//              new CounterAddCommand(
//                $name,
//                $value,
//                $status,
//                $password
//              )
//            );
//
//            // get result to pass back (or inject relevant hateoas links)
//            $result = $this->CounterViewService->execute(
//              new CounterOfIdQuery($args['id'])
//            );
//
//
//
//            $code = 201;
//        } catch (\Exception $e) {
//
//            $result = $e->getMessage();
//            $code = 409;
//
//        }
//
//        // note that even though the response is json encoded if requested
//        // as html you will get content type html showing the json.
//        //TODO: make sure there is a link to the new counter in the response
//        $response->write(json_encode($result));
//
//        return $response->withStatus($code);
//    }

    /**
     * Creating new counter.
     *
     * @param $request
     * @param $response
     * @param $args
     *
     * @return mixed
     *
     * allowing to post directly to the counters route means body needs to contain relevant data.
     *
     * @SWG\Post(
     *     path="/counters[/{id}]",
     *     operationId="newCounter",
     *     description="Creates a new Counter. Duplicates are allowed",
     *     summary="setup a new counter an existing counter",
     *     produces={"application/json"},
     *     tags={"docs"},
     *     produces={"application/json"},
     *     security={{
     *     "api_key":{},
     *         "counter_auth": {"write:counters", "read:counters"},
     *     }}
     *     @SWG\Parameter(ref="#/parameters/CounterName"),
     *     @SWG\Parameter(
     *         name="counter",
     *         in="body",
     *         description="Counter to add",
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
     *     @SWG\Response(
     *         response="AlreadyExists",
     *         description="Counter name is taken error",
     *         @SWG\Schema(ref="#/definitions/AlreadyExistsErrorModel")
     *     )
     *     @SWG\Definition(
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
     * )
     */
    public function addCounter(
      ServerRequestInterface $request,
      ResponseInterface $response,
      $args
    ) {
        // assume everything will fail
        $code = 400;
        try {
            // get at data we need from server request object to build our request
            $data = $request->getParsedBody();

            $name = $data['name'];
            $value = $data['value'];
            $status = 'active';
            $password = 'passwordplaceholder';

            // call Service that executes appropriate command with given parameters.

            $result = $this->CounterAddService->execute(
              new CounterAddCommand(
                $name,
                $value,
                $status,
                $password
              )
            );

            $code = 201;
        } catch (\Exception $e) {
            $result = $e->getMessage();
            $code = 409;
            $this->logger->info('exception ' . $e->getMessage());
        }
        // note that even though the response is json encoded if requested as html you will get content type html showing the json.
        $response->write(json_encode($result));

        return $response->withStatus($code);
    }

    /**
     * Change Counter value Route.
     *
     * @SWG\Patch(
     *     path="/counters/value[/{id}]",
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
     * incrementCounter
     *
     * try to increment a counter. will fail if counter is locked or not found.
     * if successful returns updated counter object in response
     * couner name passed as part of args array
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *   request needs to contain an integer to increment by.
     * @param \Psr\Http\Message\ResponseInterface $response
     *   a response object to return
     * @param $args
     *   the name of the counter is passed as argument. note that increment value is passed in body though.
     * @return \Psr\Http\Message\ResponseInterface|static
     *   Either an exception if counter was locked or wasnt found or the updated counter object.
     */
    public function incrementCounter(
      ServerRequestInterface $request,
      ResponseInterface $response,
      $args
    ) {

        //we assume everything is going to fail
        $result = 'an error has occurred';
        $code = 400;

        try {
            $data = $request->getParsedBody();

            $increment = $data['value'];


            $result = $this->CounterIncrementValueService->execute(
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
     * Route for changing counter state
     *
     * @param $request
     * @param $response
     * @param $args
     *
     * @return mixed
     *
     * @SWG\Patch(
     *     path="/counters/status[/{id}]",
     *     tags={"docs"},
     *     operationId="setCounterStatus",
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
     * setCounterStatus
     *
     * counter name from args, set counter status from request body
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param $args
     * @return \Psr\Http\Message\ResponseInterface|static
     */
    public function setCounterStatus(
      ServerRequestInterface $request,
      ResponseInterface $response,
      $args
    ) {
        //we assume everything is going to fail
        $result = 'an error has occurred';
        $code = 400;
        try {
            $data = $request->getParsedBody();

//            $counterName = $data['name'];
            $counterStatus = $data['status'];
            $result = $this->CounterSetStatusService->execute(
              new CounterSetStatusCommand(
                $args['id'],
                $counterStatus
              )
            );
//            $this->CounterViewService->execute(
//              new CounterOfIdQuery($args['id'])
//            );

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
     * Set Couter Route
     *
     * @SWG\Put(
     *     path="/counters/{id}",
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
     * setCounter
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function setCounter(
      ServerRequestInterface $request,
      ResponseInterface $response,
      $args
    ) {
        //we assume everything is going to fail
        $return = ['message' => 'an error has occurred'];
        $code = 400;

        try {
            // Get at data we need from server request object
            // to build our command and query.
            $data = $request->getParsedBody();
//
//            // try getting the right counter first so we know which one to increment
//            $counter = $this->CounterViewService->execute(
//              new CounterOfIdQuery()
//            );

            $return = $this->CounterResetValueService->execute(
              new CounterResetValueCommand($args['id'])
            );
            $code = 201;

        } catch (\Exception $e) {
            $return = $e->getMessage();
            $code = 409;
        }

        $body = $response->getBody();
        // now how can we allow slim response to write to body like this? and how to handle mimetypes

        $body->write(json_encode($return, JSON_UNESCAPED_SLASHES));

        return $response;
    }

    /**
     * Get Value only Route
     * @SWG\Get(
     *     path="/counters/{id}",
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
     * getCount
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param $args
     * @return \Psr\Http\Message\ResponseInterface|static
     */
    public function getCount(
      ServerRequestInterface $request,
      ResponseInterface $response,
      $args
    ) {
        //we assume everything is going to fail
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
        // slims request class gives some handy shortcuts. but we want to know how to write to responses with the basic psr7 interface
        $body->write(json_encode($result));

        return $response;
    }



    /**
     * Get Counter.
     *
     * @SWG\Get(
     *     path="/counters[/{id}]",
     *     tags={"docs"},
     *     operationId="getCounter",
     *     description="Returns a Counter if the user has access to the Counter",
     *     summary="get entire counter",
     *     @SWG\Parameter(ref="#/parameters/CounterId"),
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
     *     @SWG\Response(
     *         response="NotFound",
     *         description="counternot found error",
     *         @SWG\Schema(ref="#/definitions/NotFoundErrorModel")
     *     ),
     *   security={{
     *     "api_key":{},
     *         "counter_auth": {"write:counters", "read:counters"},
     *   }}
     *   )
     */
    /**
     * getCounter
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getCounter(
      ServerRequestInterface $request,
      ResponseInterface $response,
      $args
    ) {
        //we assume everything is going to fail
        $return = ['message' => 'an error has occurred'];
        $code = 400;
        try {

            $result = $this->CounterViewService->execute(
              new CounterOfIdQuery($args['id'])
            );
            $code = 200;
        } catch (\Exception $e) {
            $result = $e->getMessage();
            $code = 409;
        }

//        $body = $response->getBody();
//        // slims request class gives some handy shortcuts. but we want to know how to write to responses with the basic psr7 interface
//        $body->write(json_encode($result));
// note that even though the response is json encoded if requested as html you will get content type html showing the json.
        $response->write(json_encode($result->toArray()));

        return $response->withStatus($code);
    }

    /**
     * Delete Couter Route
     *
     * @SWG\Delete(
     *     path="/counters[/{id]",
     *     tags={"docs"},
     *     operationId="deleteCounter",
     *     summary="Delete counter",
     *     description="delete a counter",
     *     consumes={"application/json", "application/xml"},
     *     produces={"application/xml", "application/json"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="Counter object that needs to be updated",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/counterInput"),
     *     ),
     *     @SWG\Parameter(ref="#/parameters/CounterId"),
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
     *
     * supposedly the name of the counter to be deleted is in the request body.
     * so create a counterremoverequest with the name.
     * pass it to the counterremoval service.
     * return a response with either an json formatted error
     * TODO:  (see content negotiation on how to serve e.g. xml instead)
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteCounter(
      ServerRequestInterface $request,
      ResponseInterface $response,
      $args
    ) {
        //we assume everything is going to fail
        $return = ['message' => 'an error has occurred'];
        $code = 400;
        try {
            $result = $this->CounterRemoveService->execute(
              new CounterRemoveCommand($args['id'])
            );
            $code = 201;
        } catch (\Exception $e) {
            $code = 400;

            $result = $e->getMessage();
        }

        $body = $response->getBody();
        $body->write(json_encode($result, JSON_UNESCAPED_SLASHES));

        return $response;
    }

    /**
     * allAction.
     *
     * @return array
     */

    public function allAction()
    {
        $counters = $this->get('counter_repository')->findAll();

        return ['counter' => $counters];
    }

}
