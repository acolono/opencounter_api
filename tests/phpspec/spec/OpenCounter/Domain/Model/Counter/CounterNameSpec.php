<?php

namespace spec\OpenCounter\Domain\Model\Counter;

use OpenCounter\Domain\Model\Counter\CounterName;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CounterNameSpec extends ObjectBehavior
{

  function let()
  {
    $this->beConstructedWith('opencounter');
  }
  function it_is_initializable()
  {
    $this->shouldHaveType('OpenCounter\Domain\Model\Counter\CounterName');
  }
  function its_Name()
  {
    $this->name()->shouldReturn('opencounter');
  }
  function its_to_string()
  {
    $this->__toString()->shouldReturn('opencounter');
  }
}
