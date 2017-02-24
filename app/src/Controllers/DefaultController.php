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
