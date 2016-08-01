<?php

namespace spec\OpenCounter\Domain\Model\Counter;

use OpenCounter\Domain\Model\Counter\CounterValue;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CounterValueSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CounterValue::class);
    }
  function let()
  {
    $this->beConstructedWith('1');
  }
  function its_can_be_incremented()
  {
    $this->increment()->shouldReturn(2);
  }
}
