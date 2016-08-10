<?php

namespace spec\OpenCounter\Domain\Exception\Counter;

use OpenCounter\Domain\Exception\CounterLockedException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CounterLockedExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CounterLockedException::class);
    }
}
