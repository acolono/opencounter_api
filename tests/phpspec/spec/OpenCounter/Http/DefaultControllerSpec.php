<?php

namespace spec\OpenCounter\Http;

use Interop\Container\ContainerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use OpenCounter\http\DefaultController;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class DefaultControllerSpec extends ObjectBehavior
{
  private $logger;

  function let(ContainerInterface $ci, Logger $logger) {
//    $logger = new Logger('testlog');
//    $logger->pushProcessor(new UidProcessor());
//    $logger->pushHandler(new RotatingFileHandler('./', Logger::DEBUG));
    $this->logger = $ci->get('logger');


    $this->beConstructedWith($ci, $logger);

}
    function it_is_initializable()
    {
        $this->shouldHaveType('OpenCounter\Http\DefaultController');
    }
//  function it_shows_a_standard_index_page_as_sanity_check(ContainerInterface $ci, LoggerInterface $logger){
//
//    $this->index($request, $response, $args)->willReturn($this->renderer->render($response, 'index.phtml', $args));
//  }
//
//  function it_logs_calls_to_index(Request $request, Response $response, $args){
//
//    $this->index($request, $response, $args)->shouldBeCalled();
//    $this->logger->info()->shouldBeCalled();
//  }

//  function let(Logger $logger)
//  {
//    $this->beConstructedWith($logger);
//}
//
//
//  function it_returns_Counter_by_name(Logger $logger)
//  {
//
//    $this->findByName(5);
//$logger->debug('DB queried')->shouldHaveBeenCalled();
//
//}
}
