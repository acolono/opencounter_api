<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 2/16/17
 * Time: 3:32 PM
 */

namespace spec\SlimCounter\Application\Command\Oauth2;

use PhpSpec\ObjectBehavior;
use SlimCounter\Application\Command\Oauth2\AddClientCommand;

class AddClientCommandSpec extends ObjectBehavior
{
    function it_creates_a_client_command()
    {
        $client_id = 1;
        $client_secret = 'testsecret';
        $redirect_url = '/redirect-url';
        $scopes = 'read:counter write:counter';
        $grant_types = 'authorization_code';
        $user_id = 1;
        $this->beConstructedWith(
          $client_id,
          $client_secret,
          $redirect_url,
          $grant_types,
          $scopes,
          $user_id);
        $this->shouldHaveType(AddClientCommand::class);

// TODO: id should be generated at this point.
//        $this->id()->shouldNotBe(null);
        $this->user_id()->shouldReturn(1);
        $this->client_id()->shouldReturn(1);
        $this->client_secret()->shouldReturn('testsecret');
        $this->redirect_url()->shouldReturn('/redirect-url');
        $this->scopes()->shouldReturn('read:counter write:counter');
        $this->grant_types()->shouldReturn('authorization_code');
    }
}