<?php

namespace spec\SlimCounter\Application\Query;

use PhpSpec\ObjectBehavior;
use SlimCounter\Application\Query\listClientsHandler;
use SlimCounter\Application\Query\listClientsQuery;
use SlimCounter\Infrastructure\Persistence\Oauth2ClientRepository;

class listClientsHandlerSpec extends ObjectBehavior
{

    function let(Oauth2ClientRepository $oauth2_storage)
    {
        $this->beConstructedWith($oauth2_storage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(listClientsHandler::class);
    }

    function it_lists_all_clients_it_finds(
      listClientsQuery $query,
      Oauth2ClientRepository $oauth2_storage
    ) {
        $client_ids_array = ['array', 'of', 'client', 'ids'];

        // $query->shouldBeCalled()->willReturn($client_ids_array);
        $oauth2_storage->getAllClients()
          ->shouldBeCalled()
          ->willReturn($client_ids_array);
        $this->__invoke($query)->shouldReturn($client_ids_array);
    }
    //    function it_does_not_list_clients_if_none_exist(){
    //
    //    }

}
