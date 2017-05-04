<?php

namespace SlimCounter\Controllers;

use Interop\Container\ContainerInterface;
use OpenCounter\Http\CounterBuildService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use SlimCounter\Application\Command\Oauth2\AddClientCommand;
use SlimCounter\Application\Query\ListClientsQuery;

/**
 * Class UsersController.
 *
 * @package SlimCounter\Controllers
 */
class UsersController implements ContainerInterface
{

  /**
   * Dependency Container.
   *
   * @var \Interop\Container\ContainerInterface
   */
    protected $ci;

  /**
   * Logger.
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
   * Router.
   *
   * @var \Slim\Router
   */
    private $router;

  /**
   * CounterBuildService.
   *
   * @var \OpenCounter\Http\CounterBuildService
   */
    private $counterBuildService;

  /**
   * Constructor.
   *
   * @param \Interop\Container\ContainerInterface $ci
   */
    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
        $this->renderer = $this->ci->get('renderer');
        $this->counter_repository = $this->ci->get('counter_repository');
        $this->counterBuildService = $this->ci->get('counter_build_service');
        $this->router = $this->ci->get('router');
        $this->logger = $this->ci->get('logger');
        $this->oauth2_storage = $this->ci->get('oauth2_storage');
        $this->ListClientsService = $this->ci->get('ListClientsService');

        $this->add_client_application_service = $this->ci->get('add_client_application_service');
    }

  /**
   * AddClientForm()
   *
   * @param \Slim\Http\Request $request
   * @param \Slim\Http\Response $response
   * @param $args
   *
   * @return mixed
   */
    public function addClientForm(Request $request, Response $response, $args)
    {
        // Logging.
        $this->logger->info("admincontroller 'newUserForm' route");

        // TODO: just call an application service to fill the response.
        // Render new counter form view.
        return $this->renderer->render(
            $response,
            'admin/clients-form.html.twig'
        );
    }

  /**
   * NewClient.
   *
   * @param \Slim\Http\Request $request
   * @param \Slim\Http\Response $response
   * @param $args
   *
   * @return \Slim\Http\Response
   *
   * @throws \Exception
   */
    public function newClient(Request $request, Response $response, $args)
    {

        // TODO: just call an application service to fill the response.
        // Get at form data.
        $form_state = $request->getParsedBody();

        $data = $form_state;
        try {
            $result = $this->add_client_application_service;
            $result->execute(
                new AddClientCommand(
                    $data['client_id'],
                    $data['client_secret'],
                    $data['redirect_uri'],
                    $data['grant_types'],
                    $data['scopes'],
                    $data['user_id']
                )
            );
        } catch (ClientAlreadyExistsException $e) {
            // $form->get('email')->addError(new FormError('Email is already registered by another user'));.
        } catch (\Exception $e) {
            throw $e;
            // $form->addError(new FormError('There was an error, please get in touch with us'));.
        }

        $uri = $request->getUri()
        ->withPath($this->router->pathFor(
            'admin.client.add',
            ['client' => (array) $result]
        ));

        return $response->withRedirect((string) $uri);
    }

  /**
   * ClientsIndex.
   *
   * @param \Slim\Http\Request $request
   * @param \Slim\Http\Response $response
   * @param $args
   *
   * @return mixed
   */
    public function clientsIndex(Request $request, Response $response, $args)
    {
        // Log message.
        $this->logger->info("user controller 'index' route");
        // Call an application service that will list registered users.
        try {
            $query = $this->ListClientsService;

            $results = $query->execute(
                new ListClientsQuery()
            );
        } catch (NoClientsFoundException $e) {
            // $form->get('email')->addError(new FormError('Email is already registered by another user'));.
        } catch (\Exception $e) {
            throw $e;
            // $form->addError(new FormError('There was an error, please get in touch with us'));.
        }
        // Render index view.
        return $this->renderer->render(
            $response,
            'clients/clients-index.html.twig',
            ['data' => $results]
        );
    }

  /********************************************************************************
   * Methods to satisfy Interop\Container\ContainerInterface
   *******************************************************************************/

  /**
   * Finds an entry of the container by its identifier and returns it.
   *
   * @param string $id
   *   Identifier of the entry to look for.
   *
   * @throws ContainerValueNotFoundException  No entry was found for this
   *   identifier.
   * @throws ContainerException               Error while retrieving the
   *   entry.
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
   * Tests whether an exception needs to be recast for compliance with
   * Container-Interop.  This will be if the exception was thrown by Pimple.
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
   * Returns true if the container can return an entry for the given
   * identifier. Returns false otherwise.
   *
   * @param string $id
   *   Identifier of the entry to look for.
   *
   * @return bool
   */
    public function has($id)
    {
        return $this->offsetExists($id);
    }
}
