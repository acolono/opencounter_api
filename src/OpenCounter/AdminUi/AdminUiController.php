<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 8/6/16
 * Time: 11:46 AM
 */

namespace OpenCounter\AdminUi;


use Interop\Container\ContainerInterface;

use OpenCounter\Domain\Model\Counter\Counter;
use OpenCounter\Domain\Model\Counter\CounterName;
use OpenCounter\Domain\Model\Counter\CounterValue;

use OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlPersistentCounterRepository;
use OpenCounter\Infrastructure\Persistence\Sql\SqlManager;
use Slim\Exception\SlimException;
use Slim\Http\Request;
use Slim\Http\Response;


class AdminUiController implements ContainerInterface {
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
  }
  public function index(Request $request, Response $response, $args)
  {
    // log message
    $this->logger->info("admincontroller 'index' route");
    // Render index view
    return $this->renderer->render($response, 'admin/index.phtml', $args);
  }
  public function newCounter(Request $request, Response $response, $args)
  {
    $this->logger->info("admincontroller 'newCounter' route");
//    $counterName = new CounterName($args['name']);
//    $counter = $this->counterBuildService->execute($request, $args);
    return $this->renderer->render($response, 'admin/counter.twig');

  }
  public function viewCounter(Request $request, Response $response, $args)
  {
    $this->logger->info("admincontroller '/viewCounter' route");
    // Render a counter

    $this->logger->info('getting counter with name: '. $args['name']);
    $counterName = new CounterName($args['name']);
    $counter = $this->counter_repository->getCounterByName($counterName);

    $this->logger->info(json_encode($counter));
    if($counter){
      $this->logger->info('found');
      return $this->renderer->render($response, 'admin/counter.twig', $array =  (array) $counter);
    } else {
      // TODO: pretty error page
      $this->logger->info('not found');
      $response->write('resource not found');
      return $response->withStatus(404);
    }


  }
  public function addCounter(Request $request, Response $response, $args)
  {

    // Some post data validation logic here

    $this->logger->info('admincontroller inserting new counter with name ' . $args['name']);

    // Now we need to instantiate our Counter using a factory
    // use another service that in turn calls the factory?
    try {
      $counter = $this->counterBuildService->execute($request, $args);
      $this->counter_repository->save($counter);
      $this->logger->info('saved ' . $counter->getName());
      $return = $counter;
      $code = 201;
    } catch (\Exception $e ) {
      $return = ['message' => $e->getMessage()];
      $code = 409;

    }
    //$this->logger->error($return . $args['name'] . $code);
    return $response->withRedirect($this->router->pathFor('admin.counter.index'));

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