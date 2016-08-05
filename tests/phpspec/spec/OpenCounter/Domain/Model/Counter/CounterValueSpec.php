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
    $this->beConstructedWith(1);
  }
  function its_can_be_incremented()
  {
    $this->increment('+1')->shouldReturn(2);
  }
  function its_value_can_be_returned()
  {
    $this->value()->shouldReturn(1);
  }
}
