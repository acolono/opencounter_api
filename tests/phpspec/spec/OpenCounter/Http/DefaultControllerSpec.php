<?php

namespace spec\OpenCounter\Http;

use Interop\Container\ContainerInterface;
use OpenCounter\http\DefaultController;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DefaultControllerSpec extends ObjectBehavior
{
  function let(ContainerInterface $ci) {
    $this->beConstructedWith($ci);

}
    function it_is_initializable()
    {
        $this->shouldHaveType('OpenCounter\Http\DefaultController');
    }
}
