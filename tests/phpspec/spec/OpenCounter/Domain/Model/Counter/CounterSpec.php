<?php

namespace spec\OpenCounter\Domain\Model\Counter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;


use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Model\Counter\CounterName;

/**
 * Class CounterSpec
 * @mixin OpenCounter\Domain\Model\Counter
 * @package spec\OpenCounter\Domain\Model\Counter
 */
class CounterSpec extends ObjectBehavior
{
  function let(CounterId $counterId, CounterName $counterName, CounterValue $counterValue)
  {
    $this->beConstructedWith($counterId, $counterName, $counterValue, 'password');
  }

  function it_can_instanciate_new_counters_to_be_saved_to_collection(CounterId $counterId){
  }
  function it_should_contain_a_name_id_and_value(CounterName $counterName, CounterId $counterId, CounterValue $counterValue)
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
  function it_stores_counter_name(CounterName $counterName)
  {
    $this->getName()->shouldReturn($counterName);
  }
  function it_can_be_incremented(CounterValue $value) {

    $this->incrementValue()->willBeCalled();
    $value->increment()->willBeCalled()->willReturn($value++);
    $this->getValue()->shouldReturn($value++);

  }
//  function it_stores_counter_id(CounterId $counterId)
//  {
//    $this->getId()->shouldReturn($counterId);
//  }
//
//  function it_stores_counter_name(CounterName $counterName)
//  {
//    $this->getName()->shouldReturn($counterName);
//  }
//  function it_stores_counter_value(CounterValue $counterValue)
//  {
//    $this->getValue()->shouldReturn($counterValue);
//  }
//  function it_can_be_incremented(CounterValue $counterValue) {
//
//    $counterValue->increment()->willReturn(2);
//    $this->getValue()->shouldReturn($counterValue);
//
//  }
  function it_can_be_locked()
  {
    $this->lock();
    $this->shouldBeLocked();
  }

  function it_should_raise_exception_during_increment_if_it_is_locked()
  {
    $this->couldBeLocked()->willReturn(0);
    $this->lock();
    $this->shouldThrow('Exception')->duringLock();
  }
//  function it_stores_counter_id(CounterId $counterId)
//  {
//    $this->getId()->shouldReturn($counterId);
//  }
//
//  function it_stores_counter_name(CounterName $counterName)
//  {
//    $this->getName()->shouldReturn($counterName);
//  }
//  function it_stores_counter_value(CounterValue $counterValue)
//  {
//    $this->getValue()->shouldReturn($counterValue);
//  }
//  function it_can_be_incremented(CounterValue $counterValue) {
//
//    $counterValue->increment()->willReturn(2);
//    $this->getValue()->shouldReturn($counterValue);
//
//  }
//  function it_can_be_locked() {
//
//    $this->lock()->willBeCalled();
//    $this->isLocked()->shouldReturn('TRUE');
//
//  }
//  function it_can_be_reset() {
//
//    $this->reset()->willBeCalled();
//    $this->getValue()->shouldReturn('0');
//
//  }
  function its_password()
  {
    $this->getPassword()->shouldReturn('password');
  }
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