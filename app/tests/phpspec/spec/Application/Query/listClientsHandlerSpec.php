<?php

namespace spec\SlimCounter\Application\Query;

use OAuth2\Storage\Pdo;
use SlimCounter\Application\Query\listClientsHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SlimCounter\Application\Query\listClientsQuery;

class listClientsHandlerSpec extends ObjectBehavior
{

    function let(Pdo $oauth2_storage)
    {
        $this->beConstructedWith($oauth2_storage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(listClientsHandler::class);
    }

    function it_lists_all_clients_it_finds(
      listClientsQuery $query,
      Pdo $oauth2_storage
    ) {
        $query->shouldBeCalled()->willReturn('counter-name');
        $name = new CounterName('counter-name');
        $repository->getCounterByName($name)
          ->shouldBeCalled()
          ->willReturn($counter);
        $this->__invoke($query)->shouldReturn($counter);
    }
//    function it_does_not_list_clients_if_none_exist(){
//
//    }

}
