<?php

namespace spec\OpenCounter\Domain\Exception\Counter;

use OpenCounter\Domain\Exception\Counter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CounterAlreadyExistsExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('OpenCounter\Domain\Exception\Counter\CounterAlreadyExistsException');
    }
}
