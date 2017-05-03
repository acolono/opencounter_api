<?php

namespace spec\SlimCounter\Application\Service\Oauth;

use SlimCounter\Application\Query\listClientsHandler;
use SlimCounter\Application\Query\listClientsQuery;
use SlimCounter\Application\Service\listClientsService;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class listClientsServiceSpec extends ObjectBehavior
{

    function let(listClientsHandler $handler)
    {
        $this->beConstructedWith($handler);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(listClientsService::class);
    }

    function it_executes(
      listClientsHandler $handler,
      listClientsQuery $aQuery
    ) {
        $handler->__invoke($aQuery)->shouldBeCalled();

        $this->execute($aQuery);
    }
}
