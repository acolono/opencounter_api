<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 8/7/16
 * Time: 8:51 PM
 */

namespace OpenCounter\Api;



use Monolog\Logger;
use Slim\Http\Request;
use Slim\Http\Response;

class DefaultController
{
  private $logger;
  private $renderer;
  public function __construct(Logger $logger, $renderer)
  {
    $this->logger   = $logger;
    $this->renderer = $renderer;
  }
  public function index(Request $request, Response $response, $args)
  {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");
    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
  }
}