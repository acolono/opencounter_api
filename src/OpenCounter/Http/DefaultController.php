<?php

namespace OpenCounter\Http;

use Interop\Container\ContainerInterface;

use Slim\Http\Request;
use Slim\Http\Response;

class DefaultController
{
  protected $ci;


  public function __construct(ContainerInterface $ci)
  {
    $this->ci = $ci;
    $this->renderer = $this->ci->get('renderer');
    $this->logger = $this->ci->get('logger');
  }
  public function index(Request $request, Response $response, $args)
  {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");
    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
  }
}