<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 2/16/17
 * Time: 3:32 PM
 */

namespace spec\SlimCounter\Application\Command\Oauth2;

use OAuth2\Storage\Pdo;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SlimCounter\Application\Command\Oauth2\AddClientCommand;
use SlimCounter\Application\Command\Oauth2\AddClientHandler;

class AddClientHandlerSpec extends ObjectBehavior
{
    function let(Pdo $oauth2_storage)
    {
        $this->beConstructedWith($oauth2_storage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AddClientHandler::class);
    }

    function it_creates_the_client(
        AddClientCommand $addClientCommand,
        Pdo $oauth2_storage
    )
    {
        $addClientCommand->clientId()->shouldBeCalled()->willReturn('client_id');
        $addClientCommand->clientSecret()->shouldBeCalled()->willReturn('client_secret');
        $addClientCommand->redirectUrl()->shouldBeCalled()->willReturn('redirect_url');
        $addClientCommand->grantTypes()->shouldBeCalled()->willReturn('grant_types');
        $addClientCommand->scopes()->shouldBeCalled()->willReturn('scopes');
        $addClientCommand->userId()->shouldBeCalled()->willReturn('user_id');


        $oauth2_storage->setClientDetails(
            'client_id',
            'client_secret',
            'redirect_url',
            'grant_types',
            'scopes',
            'user_id'
        )->shouldBeCalled();

        $this->__invoke($addClientCommand);

        // TODO: client factory?
        // look for existing clients?

    }
}