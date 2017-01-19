<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 8/6/16
 * Time: 11:46 AM
 */

namespace SlimCounter\Controllers;

use Interop\Container\ContainerInterface;

use OpenCounter\Domain\Model\Counter\CounterName;

use Slim\Exception\SlimException;
use Slim\Http\Request;
use Slim\Http\Response;

class DefaultController implements ContainerInterface
{
    protected $ci;

    private $logger;
    private $counter_repository;
    private $router;
    private $counterBuildService;

    /**
     *
     */
    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
        $this->renderer = $this->ci->get('renderer');
        $this->counter_repository = $this->ci->get('counter_repository');
        $this->counterBuildService = $this->ci->get('counter_build_service');
        $this->router = $this->ci->get('router');
        $this->logger = $this->ci->get('logger');
    }

    public function index(Request $request, Response $response, $args)
    {
        // log message
        $this->logger->info("admincontroller 'index' route");

        // Render index view
        return $this->renderer->render($response, 'admin/index.twig', $args);
    }

    public function newCounterForm(Request $request, Response $response, $args)
    {
        // logging
        $this->logger->info("admincontroller 'newCounterForm' route");

        // Render new counter form view
        return $this->renderer->render($response, 'admin/counterForm.twig');
    }

    public function viewCounter(Request $request, Response $response, $args)
    {
        // logging
        $this->logger->info('admincontroller viewCounter getting counter with name: ' . $args['name']);

        $counterName = new CounterName($args['name']);
        $counter = $this->counter_repository->getCounterByName($counterName);
       // was empty
        $counterDisplay = json_encode($counter->toArray());

        // logging
        $this->logger->info($counterDisplay);

        if ($counter) {
            // logging
            $this->logger->info('found' . $counterDisplay);

            return $this->renderer->render(
                $response,
                'admin/counter.twig',
                $array = (array) $counter->toArray()
            );
        } else {
            // TODO: pretty error page
            $this->logger->info('not found');
            $response->write('resource not found');
            return $response->withStatus(404);
        }
    }

    /**
     * Add Counter Route is called by New Counter Form
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param $args
     *
     * @return \Slim\Http\Response
     */
    public function addCounter(Request $request, Response $response, $args)
    {
        // logging
        $this->logger->info('admincontroller inserting new counter from form post ' . json_encode($args));

        // Now we need to instantiate our Counter using a factory
        // use another service that in turn calls the factory?
        try {
            $counter = $this->counterBuildService->execute($request, $args);
            // logging
            $this->logger->info('admincontroller will try having repo save counter ');


            $this->counter_repository->save($counter);
            $this->counters[] = $counter;
            // logging
            $this->logger->info('saved ' . json_encode($counter->toArray()));

            $return =  json_encode($counter->toArray());
            $code = 201;
        } catch (\Exception $e) {
            $return = ['message' => $e->getMessage()];
            $code = 409;
        }
        //$this->logger->error($return . $args['name'] . $code);
        // just redirect to counter list but try to redirect to newly created counter instead
//        http://discourse.slimframework.com/t/using-response-withredirect-with-route-name-rather-than-url/212
        $uri = $request->getUri()->withPath($this->router->pathFor('admin.counter.view', ['name' => $counter->getName()]));
        return $response->withRedirect((string)$uri);
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
            throw new ContainerValueNotFoundException(sprintf(
                'Identifier "%s" is not defined.',
                $id
            ));
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
}
