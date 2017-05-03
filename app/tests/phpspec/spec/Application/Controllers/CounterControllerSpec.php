<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 8/6/16
 * Time: 12:42 PM
 */

namespace spec\SlimCounter\Controllers;

use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use OpenCounter\Domain\Model\Counter\Counter;
use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Model\Counter\CounterName;
use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Domain\Repository\CounterRepository;
use OpenCounter\Http\CounterBuildService;
use OpenCounter\Infrastructure\Persistence\StorageInterface;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Slim\Http\Request;
use Slim\Http\Response;
use SlimCounter\Application\Service\Counter\CounterAddService;
use SlimCounter\Application\Service\Counter\CounterIncrementValueService;
use SlimCounter\Application\Service\Counter\CounterRemoveService;
use SlimCounter\Application\Service\Counter\CounterViewService;

/**
 * Class CounterControllerSpec
 * @package spec\OpenCounter\Http
 *
 * A controller to respond to requests
 * that is not getting the container passed to it
 * but inflects on dependencies to inject.
 */
class CounterControllerSpec extends ObjectBehavior
{
    private $logger;
    private $counterBuildService;
    private $counter_mapper;
    private $counter_repository;
    private $CounterAddService;
    private $CounterRemoveService;
    private $CounterIncrementValueService;
    private $CounterViewService;

    function let(
      LoggerInterface $logger,
      CounterBuildService $counterBuildService,
      StorageInterface $counter_mapper,
      CounterRepository $counter_repository,
      CounterAddService $CounterAddService,
      CounterRemoveService $CounterRemoveService,
      CounterIncrementValueService $CounterIncrementValueService,
      CounterViewService $CounterViewService
    ) {
        $this->beConstructedWith(
          $logger,
          $counterBuildService,
          $counter_mapper,
          $counter_repository,
          $CounterAddService,
          $CounterRemoveService,
          $CounterIncrementValueService,
          $CounterViewService
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('SlimCounter\Controllers\CounterController');
    }

    function its_remove_method_deletes_counters(
      ServerRequestInterface $request,
      ResponseInterface $response
    ) {
        // mock a counter we have
        $object = new Counter(
          new CounterId('1'),
          new CounterName('demo'),
          new CounterValue('1'),
          'active',
          'password'
        );
        $this->counter_repository->getCounterByName('demo')
          ->willReturn($object);

        // which counter gets deleted is derived from the information in the body or from the path?
        $delete_request_path = '';
        $delete_request_body = array();
        $request->getParsedBody()->willReturn($delete_request_body);
        $request->getUri()->willReturn($delete_request_path);

        // mock a delete counter request to be passed to the Service when calling the deleteCounter Method

        // it will tell the delete counter service to execute the request.
        // this is a command that doesnt give feedback
        $this->deleteCounterService()->execute($request)->shouldBeCalled();

        // call the appropriate controller method
        $response = $this->deleteCounter($request, 1);
        $response->shouldBeAnInstanceOf('ResponseInterface');

        // ensure the appropriate counter was deleted
        // TODO: seperate spec for failed Service
    }
}
