<?php

namespace spec\SlimCounter\Application\Query;

use SlimCounter\Application\Query\listClientsQuery;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class listClientsQuerySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(listClientsQuery::class);
    }
}
