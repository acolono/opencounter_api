<?php

namespace spec\OpenCounter\Domain\Model\Counter;

use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Model\Counter\CounterName;
use OpenCounter\Domain\Model\Counter\CounterValue;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;


/**
 * Class CounterSpec
 * @package spec\OpenCounter\Domain\Model\Counter
 */
class CounterSpec extends ObjectBehavior
{
    function let(
      CounterId $counterId,
      CounterName $counterName,
      CounterValue $counterValue,
      $status
    ) {
        $counterId = new CounterId('acounteruuid');
        $counterName = new CounterName('counterone');
        $counterValue = new CounterValue(1);
        $status = 'active';

        $this->beConstructedWith($counterId, $counterName, $counterValue,
          $status, 'password');

    }

    function it_can_be_incremented_if_its_not_locked(
      CounterId $counterId,
      CounterName $counterName,
      CounterValue $counterValue
    ) {
        $increment = new CounterValue(1);

        $this->shouldNotBeLocked();

        $this->increaseCount($increment)->shouldReturn(true);

        $this->getValue()->shouldReturn(2);

    }

    function it_can_not_be_incremented_if_its_locked()
    {
        $this->lock();
        $increment = new CounterValue(1);
        $this->shouldBeLocked();

        $this->shouldThrow('OpenCounter\Domain\Exception\Counter\CounterLockedException')
          ->duringIncreaseCount($increment);

    }

    function it_stores_counter_id_as_value_object(CounterId $counterId)
    {
        $this->getId()->shouldReturn("acounteruuid");
    }

    function it_stores_counter_value(CounterValue $value)
    {
        $this->getValue()->shouldReturn(1);
    }

    function it_returns_the_name_it_is_created_with(CounterName $counterName)
    {
        $this->getName()->shouldReturn('counterone');
    }

    function it_can_be_locked()
    {
        $this->lock();
        $this->shouldBeLocked();
    }
//
//  function it_can_be_reset() {
//    $otherCounterValue = new CounterValue(2);
//    $this->resetValueTo($otherCounterValue)->shouldReturn(3);;
//    $this->getValue()->shouldReturn(3);
//
//  }
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