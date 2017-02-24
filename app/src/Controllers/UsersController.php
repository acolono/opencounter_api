<?php
/**
 * UsersController
 *
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 8/6/16
 * Time: 11:46 AM
 *
 * TODO: consider routing all requests through a single method since ideally its always just a matter of calling the right service and returning the right template (or errorpage on exception)
 */

namespace SlimCounter\Controllers;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

use SlimCounter\Application\Command\Oauth2\AddClientCommand;

class UsersController implements ContainerInterface
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
        $this->oauth2_storage = $this->ci->get('oauth2_storage');

        $this->add_client_application_service = $this->ci->get('add_client_application_service');
    }

    public function addClientForm(Request $request, Response $response, $args)
    {
        // logging
        $this->logger->info("admincontroller 'newUserForm' route");

        // TODO: just call an application service to fill the response

        // Render new counter form view
        return $this->renderer->render(
          $response,
          'admin/clients-form.html.twig'
        );
    }

    public function newClient(Request $request, Response $response, $args)
    {

        // TODO: just call an application service to fill the response

        // get at form data

        $form_state = $request->getParsedBody();

        $data = $form_state;
        // call an application service that will list registered users.
        try {
//            $result = $this->ci->get('add_user_application_service')
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
//            $form->get('email')->addError(new FormError('Email is already registered by another user'));
        } catch (\Exception $e) {
            throw $e;
//            $form->addError(new FormError('There was an error, please get in touch with us'));
        }
        // TODO: consider adding oauth_users along with local users.
        //$this->storage->setUser($username, $password, $firstName = null, $lastName = null);

        // TODO: provie a generated client id and secret to each user.

        //$this->storage->setClientDetails($client_id, $client_secret, $redirect_uri, $grant_types, $scopes, $user_id);

        $uri = $request->getUri()
          ->withPath($this->router->pathFor(
            'admin.client.add',
            ['client' => (array)$result]
          ));

        return $response->withRedirect((string)$uri);
    }

    public function index(Request $request, Response $response, $args)
    {
        // log message
        $this->logger->info("user controller 'index' route");

        // Render index view
        return $this->renderer->render($response, 'index.twig', $args);
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
