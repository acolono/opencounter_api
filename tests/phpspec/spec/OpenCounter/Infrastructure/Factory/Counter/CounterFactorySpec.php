<?php

namespace spec\OpenCounter\Infrastructure\Factory\Counter;

use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Model\Counter\CounterName;
use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Infrastructure\Factory\CounterFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CounterFactorySpec extends ObjectBehavior
{
    function it_implements_counter_factory_interface()
    {
        $this->shouldHaveType('OpenCounter\Infrastructure\Factory\Counter\CounterFactory');
        //$this->shouldImplement('OpenCounter\Domain\Factory\Counter\CounterFactory');
    }

    function it_builds(
      CounterId $userId,
      CounterName $name,
      CounterValue $value
    ) {
        $this->build($userId, $name, $value, 'active', 'password')
          ->shouldReturnAnInstanceOf('OpenCounter\Domain\Model\Counter\Counter');
    }
}
