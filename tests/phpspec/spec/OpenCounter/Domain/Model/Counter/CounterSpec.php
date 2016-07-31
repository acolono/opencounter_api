<?php

namespace spec\OpenCounter\Domain\Model\Counter;

use OpenCounter\Domain\Model\Counter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;


use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Domain\Model\Counter\CounterId;


class CounterSpec extends ObjectBehavior
{
  function let(CounterId $counterId, CounterValue $value)
  {
    $this->beConstructedWith($counterId, $value, 'password');
  }
  function it_is_initializable()
  {
    $this->shouldHaveType('OpenCounter\Domain\Model\Counter\Counter');
  }
  function it_stores_counter_id(CounterId $counterId)
  {
    $this->getId()->shouldReturn($counterId);
  }
  function it_stores_counter_value(CounterValue $value)
  {
    $this->getValue()->shouldReturn($value);
  }
  function it_can_be_incremented(CounterValue $value) {

    $value->incrementValue()->willReturn(2);
    $this->getValue()->shouldReturn($value);

  }
//  function its_password()
//  {
//    $this->getPassword()->shouldReturn('password');
//  }
//  function it_does_not_change_the_password_because_it_is_invalid_password()
//  {
//    $this->shouldThrow(new \InvalidArgumentException('password'))->during('changePassword', [' ']);
//  }
//  function it_changes_the_password()
//  {
//    $this->changePassword('newpassword')->shouldReturn($this);
//    $this->password()->shouldReturn('newpassword');
//  }
}