<?php

namespace spec\SlimCounter\Application\Service\Oauth;

use SlimCounter\Application\Query\ListClientsHandler;
use SlimCounter\Application\Query\ListClientsQuery;
use SlimCounter\Application\Service\ListClientsService;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ListClientsServiceSpec extends ObjectBehavior
{

    function let(ListClientsHandler $handler)
    {
        $this->beConstructedWith($handler);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ListClientsService::class);
    }

    function it_executes(
      ListClientsHandler $handler,
      ListClientsQuery $aQuery
    ) {
        $handler->__invoke($aQuery)->shouldBeCalled();

        $this->execute($aQuery);
    }
}
