<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 8/6/16
 * Time: 11:46 AM
 */

namespace OpenCounter\Http;


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
class CounterController implements ContainerInterface
{
    protected $ci;

    private $logger;
    private $counter_repository;
    private $counterBuildService;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;

        $this->logger = $this->ci->get('logger');
        $this->counterBuildService = $this->ci->get('counter_build_service');
        $this->SqlManager = $this->ci->get('counter_mapper');
        $this->counter_repository = $this->ci->get('counter_repository');

    }


    public function newCounter($request, $response, $args)
    {

        $this->logger->info('inserting new counter with name ' . $args['name']);

        // Now we need to instantiate our Counter using a factory
        // use another service that in turn calls the factory?
        try {
            $counter = $this->counterBuildService->execute($request, $args);
            $this->counter_repository->save($counter);
            $this->logger->info('saved ' . $counter->getName());
            $return = $counter->toArray();
            $code = 201;
        } catch (\Exception $e) {

            $return = ['message' => $e->getMessage()];
            $code = 409;
            $this->logger->info('exception ' . $e->getMessage());
        }


        return $response->withJson($return, $code);

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
    public function addCounter()
    {

    }


    public function incrementCounter($request, $response, $args)
    {

        $this->logger->info('incrementing (PATCH) counter with name ' . $args['name']);
        //we assume everything is going to fail
        $return = ['message' => 'an error has occurred'];
        $code = 400;

        $data = $request->getParsedBody();
        $this->logger->info(json_encode($data));

        $counterName = new CounterName($args['name']);

        // validate the array
        if ($data && isset($data['value'])) {
            $counter = $this->counter_repository->getCounterByName($counterName);

            if ($counter) {
                if ($counter->isLocked()) {
                    $return['message'] = 'counter with name onecounter is locked';
                    $code = 409;
                } else {
                    $increment = new CounterValue($data['value']);
                    $update = $counter->increaseCount($increment);
                    if ($update) {
                        $this->counter_repository->update($counter);
                        $return = $counter->toArray();;
                        $code = 201;
                    }
                }
            } else {
                $return['message'] = 'The counter was not found, possibly due to bad credentials';
                $code = 404;
            }
        }
        return $response->withJson($return, $code);

    }

    public function setCounterStatus($request, $response, $args)
    {
        $this->logger->info('updating (PATCH) status of counter with name ' . $args['name']);
        //we assume everything is going to fail
        $return = ['message' => 'an error has occurred'];
        $code = 400;

        $data = $request->getParsedBody();
        $this->logger->info(json_encode($data));

        $counterName = new CounterName($args['name']);
        $counterValue = new CounterValue($data['value']);
        // validate the array
        if ($data && isset($data['status']) && $data['status'] = 'locked') {
            $counter = $this->counter_repository->getCounterByName($counterName);
            if ($counter) {
                if ($counter->isLocked()) {
                    $return['message'] = 'The counter is locked already';
                    $code = 409;
                } else {
                    $counter->lock();
                    $this->counter_repository->save($counter);
                    $this->logger->info('saved locked counter' . $counterName);
                    $return = $counter->toArray();;
                    $code = 201;
                }
            } else {
                $return['message'] = 'The counter was not found, possibly due to bad credentials';
                $code = 404;
            }
        }
        return $response->withJson($return, $code);

    }

    public function setCounter($request, $response, $args)
    {
        $this->logger->info('updating (PUT) counter with name ' . $args['name']);
        //we assume everything is going to fail
        $return = ['message' => 'an error has occurred'];
        $code = 400;

        $data = $request->getParsedBody();
        $this->logger->info(json_encode($data));

        $counterName = new CounterName($args['name']);
        $counterValue = new CounterValue($data['value']);

        // validate the array
        if ($data && isset($data['value'])) {
            $counter = $this->counter_repository->getCounterByName($counterName);

            if ($counter) {
                $this->logger->info('found ' . $counterName);

                if ($counter->isLocked()) {
                    $this->logger->info('cannot save locked counter  ' . $counterName);

                    $return['message'] = 'counter with name ' . $counterName . ' is locked';
                    $code = 409;
                } else {
                    $counter->resetValueTo($counterValue);

                    $this->counter_repository->save($counter);
                    $this->logger->info('saved ' . $counterName);
                    $return = $counter->toArray();
                    $code = 201;
                }
            } else {
                $this->logger->info('The counter was not found ' . $counterName);

                $return['message'] = 'The counter was not found, possibly due to bad credentials';
                $code = 404;
            }
        }
        return $response->withJson($return, $code);

    }

    public function getCount($request, $response, $args)
    {

        $this->logger->info('getting value from counter with name: ' . $args['name']);

        $counterName = new CounterName($args['name']);
        $counter = $this->counter_repository->getCounterByName($counterName);
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

    public function getCounter($request, $response, $args)
    {

        $this->logger->info('getting counter with name: ' . $args['name']);
        $counterName = new CounterName($args['name']);
        $counter = $this->counter_repository->getCounterByName($counterName);

        $this->logger->info(json_encode($counter));
        if ($counter) {
            $this->logger->info('found');
            return $response->withJson($counter->toArray(), 200);
        } else {
            $this->logger->info('not found');
            //$response->write('resource not found');
            return $response->withStatus(404);
        }
    }

    public function allAction()
    {
        $counters = $this->get('counter_repository')->findAll();

        return array('counter' => $counters);
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
            throw new ContainerValueNotFoundException(sprintf('Identifier "%s" is not defined.',
              $id));
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
    private function exceptionThrownByContainer(
      \InvalidArgumentException $exception
    ) {
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

    public function findAction($argument1)
    {
        // TODO: write logic here
    }

}