<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 8/6/16
 * Time: 12:42 PM
 */

namespace spec\OpenCounter\Http;

use OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlCounterRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
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
  //
//  function let(SqlPersistentCounterRepository $repository, Logger $logger, CounterBuildService $counterBuildService)
//  {
//    $this->setContainer($container);
//    $this->sqlCounterRepository = $sqlCounterRepository;
//    $this->beConstructedWith(
//      $sqlCounterRepository
//    );
//
//  }
//  function it_is_initializable()
//  {
//    $this->shouldHaveType('OpenCounter\Infrastructure\Api\CounterController');
//
//  }
//  function it_shows_a_single_counter(
//    SqlCounterRepository $sqlCounterRepository,
//    Response $response
//  ) {
//    $repository->find(1)->willReturn('A counter');
//
//    $this->showAction(1)->shouldReturn($response);
//  }
//  function it_throws_an_exception_if_a_counter_doesnt_exist(CounterRepository $repository)
//  {
//    $id = 99;
//    $sqlCounterRepository->find($id)->willReturn(null);
//    $this
//      ->shouldThrow(new CounterException(
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
//    $counter = $this->getCounter('onecounter');
//    $counter->shouldBeAnInstanceOf('OpenCounter\Domain\Model\Counter\Counter');
//  }
//  function it_receives_patch_requests_from_counter_route_to_reset_counter(){}
//  function it_receives_patch_requests_from_counter_route_to_lock_counter(){}
//  function it_receives_put_requests_from_counter_route_to_increment_counter(){}
}