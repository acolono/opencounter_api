<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 8/6/16
 * Time: 12:42 PM
 */

namespace spec\OpenCounter\Http;

use Interop\Container\ContainerInterface;
use OpenCounter\Domain\Exception\Counter\CounterNotFoundException;
use OpenCounter\Domain\Repository\CounterRepositoryInterface;
use OpenCounter\Domain\Repository\PersistentCounterRepositoryInterface;
use OpenCounter\Http\CounterBuildService;
use OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlCounterRepository;
use OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlPersistentCounterRepository;
use PhpSpec\ObjectBehavior;
use Pimple\Container;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Slim\Exception\SlimException;
use Slim\Http\Response;

/**
 * Class CounterControllerSpec
 * @package spec\OpenCounter\Http
 *
 * A container aware controller to respond to requests.
 */
class CounterControllerSpec extends ObjectBehavior
{
  protected $container;

  private $logger;
  private $counter_repository;
  private $counterBuildService;

  function let(ContainerInterface $ci) {
    $this->beConstructedWith($ci);

  }
  function it_is_initializable(ContainerInterface $container)
  {
    $this->shouldHaveType('OpenCounter\Http\CounterController');

  }
//  function it_shows_a_single_counter(
//    ContainerInterface $container,
//    PersistentCounterRepositoryInterface $repository,
//    Response $response
//  ) {
//
//    $settings = ['foo' => 'FOO', 'bar' => 'BAR'];
//    $app = new \Slim\App($settings);
//    $container = $app->getContainer();
//
//    $this->setContainer($container);
//    $this->repository = $repository;
//    $this->beConstructedWith(
//      $container,
//      $repository,
//      $logger
//    );
//    $this->repository->find(1)->willReturn('A counter');
//
//    $this->showAction(1)->shouldReturn($response);
//  }
//  function it_throws_an_exception_if_a_counter_doesnt_exist(CounterRepositoryInterface $repository)
//  {
//    $id = 99;
//    $repository->find($id)->willReturn(null);
//    $this
//      ->shouldThrow(new CounterNotFoundException(
//        sprintf('Counter [%s] cannot be found.', $id)
//      ))
//      ->duringFindAction(999)
//    ;
//  }
//
//
//
//  function it_adds_counter_repository_to_container(){
//
//  }
//  function it_receives_post_requests_from_counter_route(){
//    $this->newCounter();
//  }
//  function it_receives_get_requests_from_counter_route_asking_for_counter_by_name(){
//    $counter = $this->getCounter($request, $response, $args);
//    $counter->shouldBeAnInstanceOf('OpenCounter\Domain\Model\Counter\Counter');
//  }
//  function it_receives_patch_requests_from_counter_route_to_reset_counter(){}
//  function it_receives_patch_requests_from_counter_route_to_lock_counter(){}
//  function it_receives_put_requests_from_counter_route_to_increment_counter(){}
}