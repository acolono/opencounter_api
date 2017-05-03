<?php

namespace spec\SlimCounter\Application\Query;

use SlimCounter\Application\Query\ListClientsQuery;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ListClientsQuerySpec extends ObjectBehavior
{

    public function it_creates_a_query()
    {
        $this->shouldHaveType(ListClientsQuery::class);
    }
}
