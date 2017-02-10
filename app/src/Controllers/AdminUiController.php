<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 8/6/16
 * Time: 11:46 AM
 */

namespace SlimCounter\Controllers;

use Application\Service\Counter\AddCounterService;
use BenGorUser\User\Application\Command\SignUp\SignUpUserHandler;
use Interop\Container\ContainerInterface;

use OpenCounter\Domain\Model\Counter\CounterName;

use OpenCounter\Domain\Model\Counter\CounterValue;
use Slim\Exception\SlimException;
use Slim\Http\Request;
use Slim\Http\Response;
use SlimCounter\Application\Command\Counter\CounterAddCommand;
use SlimCounter\Application\Command\Counter\CounterViewCommand;
use SlimCounter\Application\Command\Counter\ViewCounterRequest;
use SlimCounter\Application\Query\CounterOfNameQuery;
use SlimCounter\Application\Service\Counter\AddCounterRequest;
use SlimCounter\Application\Service\User\AddUserRequest;
use SlimCounter\Application\Service\User\ViewUsersRequest;

class AdminUiController implements ContainerInterface
{
    protected $ci;

    private $logger;
    private $counter_repository;
    private $router;
    private $counterBuildService;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
        $this->renderer = $this->ci->get('renderer');
        $this->counter_repository = $this->ci->get('counter_repository');
        $this->counterBuildService = $this->ci->get('counter_build_service');
        $this->router = $this->ci->get('router');
        $this->logger = $this->ci->get('logger');
        $this->storage = $this->ci->get('pdo');
        $this->CounterViewService = $this->ci->get('CounterViewService');
        $this->CounterAddService = $this->ci->get('CounterAddService');
    }


    /**
     * New Counter form
     *
     * basic form which submits to a different route.
     * currently just for adding not for editing
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param $args
     * @return mixed
     */
    public function newCounterForm(Request $request, Response $response, $args)
    {
        // logging
        $this->logger->info("admincontroller 'newCounterForm' route");

        // Render new counter form view
        return $this->renderer->render(
            $response,
            'admin/counter-form.html.twig'
        );
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return static
     */

    public function viewCounter(Request $request, Response $response, $args)
    {
        try {
            $result = $this->CounterViewService->execute(
              new CounterOfNameQuery($args['name'])
            );

            $response->withJson($result);
        } catch (\Exception $e) {
            $return = ['message' => $e->getMessage()];
            $code = 409;
            $response->write('resource not found');
            return $response->withStatus(404);
        }

        return $this->renderer->render(
            $response,
          'admin/view-counter.html.twig'
          , $result->toArray()
        );
//
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

        try {
            $data = $request->getParsedBody();

            $name = new CounterName($data['name']);
            $value = new CounterValue($data['value']);
            $status = 'active';
            $password = 'passwordplaceholder';

            // call Service that executes appropriate command with given parameters.

            $code = 201;

            $result = $this->CounterAddService
              ->execute(new CounterAddCommand(
                  $name,
                  $value,
                  $status,
                  $password
              ));
        } catch (\Exception $e) {
            $return = ['message' => $e->getMessage()];
            $code = 409;
        }
        // just redirect to counter list but try to redirect to newly created counter instead
        // http://discourse.slimframework.com/t/using-response-withredirect-with-route-name-rather-than-url/212
        $uri = $request->getUri()
          ->withPath($this->router->pathFor(
              'admin.counter.view',
              ['name' => $result['name']]
          ));
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
