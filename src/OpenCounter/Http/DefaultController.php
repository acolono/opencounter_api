<?php

namespace OpenCounter\Http;

use Interop\Container\ContainerInterface;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class DefaultController
 * @package OpenCounter\Http
 */
class DefaultController
{
    /**
     * @var \Interop\Container\ContainerInterface
     */
    protected $ci;

    /**
     * Constructor
     *
     * @param \Interop\Container\ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
        $this->renderer = $this->ci->get('renderer');
        $this->logger = $this->ci->get('logger');
    }

    /**
     * Default Controller Index
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param $args
     *
     * @return mixed
     */
    public function index(Request $request, Response $response, $args)
    {
        // Sample log message
        $this->logger->info("Slim-Skeleton '/' route");
        // Render index view
        return $this->renderer->render($response, 'index.phtml', $args);
    }
}