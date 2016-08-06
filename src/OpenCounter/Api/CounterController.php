<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 8/6/16
 * Time: 11:46 AM
 */

namespace OpenCounter\Api;


use Interop\Container\ContainerInterface;
use OpenCounter\Domain\Model\Counter\Counter;
use OpenCounter\Domain\Model\Counter\CounterName;
use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlPersistentCounterRepository;
use OpenCounter\Infrastructure\Persistence\Sql\SqlManager;
use Slim\Exception\SlimException;

/**
 * Class CounterController
 * @package OpenCounter\Api
 */
class CounterController implements ContainerInterface{

  protected $ci;

  /**
   * @param \Interop\Container\ContainerInterface $ci
   */
  public function __construct(ContainerInterface $ci) {
    $this->ci = $ci;
    $this->logger = $this->ci->get('logger');
    $counter_mapper = new SqlManager($this->ci->get('db'));
    $this->counterRepository = new SqlPersistentCounterRepository($counter_mapper);
  }


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
  public function newCounter($request, $response, $args) {
    $this->logger->info('inserting new counter with name ' . $args['name']);
    $data = $request->getParsedBody();
    $this->logger->info(json_encode($data));

    if(!isset($data)){
      $data = [ 'value' => 0, 'name' => 'OneCounter' ];
    }
    // Persisting a new counter
    // https://leanpub.com/ddd-in-php/read#leanpub-auto-persisting-value-objects

    $counterId = $this->counterRepository->nextIdentity();
    $counterName = new CounterName($args['name']);
    $counterValue = new CounterValue($data['value']);
    $counter = new Counter($counterName, $counterId, $counterValue, 'passwordplaceholder');

    // dealing with duplicates
    if ($this->counterRepository->getCounterByName($counterName)) {
      return $response->withJson(
        ['message' => 'counter with name '. $counter->getName() . ' already exists'],
        409
      );
    }
    else {
      $this->counterRepository->save($counter);
      $this->logger->info('saved ' . $counterName);
      return $response->withJson($counter, 201);
    }

  }
  /**
   * @SWG\Post(
   *     path="/counters",
   *     operationId="addCounter",
   *     description="Creates a new counter. Duplicates are allowed",
   *     tags={"docs"},
   *     produces={"application/json"},
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
   *     )
   * )
   */
  public function addCounter(){

  }


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
  public function incrementCounter($request, $response, $args) {

    $this->logger->info('updating (PUT) counter with name ' . $args['name']);
    //we assume everything is going to fail
    $return = ['message' => 'an error has occurred'];
    $code = 400;

    $data = $request->getParsedBody();
    $this->logger->info(json_encode($data));

    $counterName = new CounterName($args['name']);

    // validate the array
    if($data && isset($data['value'])){
      $counter = $this->counterRepository->getCounterByName($counterName);
      if($counter){
        if ($counter->isLocked()) {
          $return['message'] = 'The counter is locked and cannot be changed';
          $code = 409;
        }
        else {

          $update = false;
          if($data['value'] === '+1'){
            $counter->value++;
            $update = true;
          } else if($data['value'] === '-1'){
            $counter->value--;
            $update = true;
          } else if(is_int($data['value'])){
            $counter->value = $data['value'];
            $update = true;
          } else {
            $return['message'] = 'Not a valid value, it should be either an integer or a "+1" or "-1" string';
          }

          if($update){
            $this->counterRepository->update($counter);
            $return = $counter;
            $code = 201;
          }
        }
      }
      else {
        $return['message'] = 'The counter was not found, possibly due to bad credentials';
        $code = 404;
      }
    }
    return $response->withJson($return, $code);

  }

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
  public function setCounterStatus($request, $response, $args) {
    $this->logger->info('updating (PUT) counter with name ' . $args['name']);
    //we assume everything is going to fail
    $return = ['message' => 'an error has occurred'];
    $code = 400;

    $data = $request->getParsedBody();
    $this->logger->info(json_encode($data));

    $counterName = new CounterName($args['name']);
    $counterValue = new CounterValue($data['value']);
    // validate the array
    if($data && isset($data['value'])){
      $counter = $this->counterRepository->getCounterByName($counterName);
      if($counter) {
        if ($counter->isLocked()) {
          $return['message'] = 'The counter is locked and cannot be changed';
          $code = 409;
        }
        else {
          $this->counterRepository->save($counter);
          $this->logger->info('saved ' . $counterName);
          $return = $counter;
          $code = 201;
        }
      }
      else{
        $return['message'] = 'The counter was not found, possibly due to bad credentials';
        $code = 404;
      }
    }
    return $response->withJson($return, $code);

  }

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
  public function setCounter($request, $response, $args) {
    $this->logger->info('updating (PUT) counter with name ' . $args['name']);
    //we assume everything is going to fail
    $return = ['message' => 'an error has occurred'];
    $code = 400;

    $data = $request->getParsedBody();
    $this->logger->info(json_encode($data));

    $counterName = new CounterName($args['name']);
    $counterValue = new CounterValue($data['value']);
    // validate the array
    if($data && isset($data['value'])){
      $counter = $this->counterRepository->getCounterByName($counterName);
      if($counter) {
        if ($counter->isLocked()) {
          $return['message'] = 'The counter is locked and cannot be changed';
          $code = 409;
        }
        else {
          $this->counterRepository->save($counter);
          $this->logger->info('saved ' . $counterName);
          $return = $counter;
          $code = 201;
        }
      }
      else{
        $return['message'] = 'The counter was not found, possibly due to bad credentials';
        $code = 404;
      }
    }
    return $response->withJson($return, $code);

  }

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
  public function getCount($request, $response, $args) {

    $this->logger->info('getting value from counter with name: ' . $args['name']);

    $counterName = new CounterName($args['name']);
    $counter = $this->counterRepository->getCounterByName($counterName);
    $this->logger->info(json_encode($counter));

    if ($counter) {
      $this->logger->info('found');
      return $response->withJson($counter->getValue());
    } else {
      $this->logger->info('not found');
      //$response->write('resource not found');
      return $response->withStatus(404);
    }
  }


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
  public function getCounter($request, $response, $args) {

    $this->logger->info('getting counter with name: '. $args['name']);
    $counterName = new \OpenCounter\Domain\Model\Counter\CounterName($args['name']);
    $counter = $this->counterRepository->getCounterByName($counterName);

    $this->logger->info(json_encode($counter));
    if($counter){
      $this->logger->info('found');
      return $response->withJson($counter, 200);
    } else {
      $this->logger->info('not found');
      //$response->write('resource not found');
      return $response->withStatus(404);
    }
  }
  public function allAction()
  {
    $users = $this->get('counterRepository')->findAll();

    return array('counter' => $counter);
  }
  /********************************************************************************
   * Methods to satisfy Interop\Container\ContainerInterface
   *******************************************************************************/

  /**
   * Finds an entry of the container by its identifier and returns it.
   *
   * @param string $id Identifier of the entry to look for.
   *
   * @throws ContainerValueNotFoundException  No entry was found for this identifier.
   * @throws ContainerException               Error while retrieving the entry.
   *
   * @return mixed Entry.
   */
  public function get($id)
  {
    if (!$this->offsetExists($id)) {
      throw new ContainerValueNotFoundException(sprintf('Identifier "%s" is not defined.', $id));
    }
    try {
      return $this->offsetGet($id);
    } catch (\InvalidArgumentException $exception) {
      if ($this->exceptionThrownByContainer($exception)) {
        throw new SlimContainerException(
          sprintf('Container error while retrieving "%s"', $id),
          null,
          $exception
        );
      } else {
        throw $exception;
      }
    }
  }

  /**
   * Tests whether an exception needs to be recast for compliance with Container-Interop.  This will be if the
   * exception was thrown by Pimple.
   *
   * @param \InvalidArgumentException $exception
   *
   * @return bool
   */
  private function exceptionThrownByContainer(\InvalidArgumentException $exception)
  {
    $trace = $exception->getTrace()[0];

    return $trace['class'] === PimpleContainer::class && $trace['function'] === 'offsetGet';
  }

  /**
   * Returns true if the container can return an entry for the given identifier.
   * Returns false otherwise.
   *
   * @param string $id Identifier of the entry to look for.
   *
   * @return boolean
   */
  public function has($id)
  {
    return $this->offsetExists($id);
  }

}