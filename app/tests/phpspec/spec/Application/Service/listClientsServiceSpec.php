<?php

namespace spec\SlimCounter\Application\Service;

use SlimCounter\Application\Service\listClientsService;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class listClientsServiceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(listClientsService::class);
    }
}
