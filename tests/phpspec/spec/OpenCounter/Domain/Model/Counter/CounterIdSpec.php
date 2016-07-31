<?php

namespace spec\OpenCounter\Domain\Model\Counter;

use OpenCounter\Domain\Model\Counter\CounterId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;


class CounterIdSpec extends ObjectBehavior
{
  function let()
  {
    $this->beConstructedWith('theid');
  }
  function it_is_initializable()
  {
    $this->shouldHaveType('OpenCounter\Domain\Model\Counter\CounterId');
  }
  function its_id()
  {
    $this->id()->shouldReturn('theid');
  }
  function it_should_not_be_equals(CounterId $counterId)
  {
    $counterId->id()->shouldBeCalled()->willReturn('otherid');
    $this->equals($counterId)->shouldReturn(false);
  }
  function it_should_be_equals(CounterId $counterId)
  {
    $counterId->id()->shouldBeCalled()->willReturn('theid');
    $this->equals($counterId)->shouldReturn(true);
  }
  function its_to_string()
  {
    $this->__toString()->shouldReturn('theid');
  }
}