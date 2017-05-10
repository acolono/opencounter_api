<?php

namespace spec\SlimCounter\Application\Query;

use PhpSpec\ObjectBehavior;
use SlimCounter\Application\Query\ListClientsHandler;
use SlimCounter\Application\Query\ListClientsQuery;
use SlimCounter\Infrastructure\Persistence\Oauth2Repository;

class ListClientsHandlerSpec extends ObjectBehavior
{

    public function let(Oauth2Repository $oauth2_storage)
    {
        $this->beConstructedWith($oauth2_storage);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ListClientsHandler::class);
    }

    public function it_lists_all_clients_it_finds(
        ListClientsQuery $query,
        Oauth2Repository $oauth2_storage
    ) {
        $client_ids_array = ['array', 'of', 'client', 'ids'];

        $oauth2_storage->getAllClients()
          ->shouldBeCalled()
          ->willReturn($client_ids_array);
        $this->__invoke($query)->shouldReturn($client_ids_array);
    }
    //    function it_does_not_list_clients_if_none_exist(){
    //
    //    }
}
