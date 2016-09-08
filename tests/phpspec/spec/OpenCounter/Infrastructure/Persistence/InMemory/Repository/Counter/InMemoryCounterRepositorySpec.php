<?php

namespace spec\OpenCounter\Infrastructure\Persistence\InMemory\Repository\Counter;

use OpenCounter\Domain\Model\Counter\Counter;
use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Model\Counter\CounterName;
use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Infrastructure\Persistence\InMemory\Repository\Counter\InMemoryCounterRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InMemoryCounterRepositorySpec extends ObjectBehavior
{
    function let(){
        $counters = [];
        $counters[] = new Counter(
            new CounterId('1CE05088-ED1F-43E9-A415-3B3792655A9B'),
            new CounterName('onecounter'), new CounterValue(2), 'active',
            'passwordplaceholder'
        );
        $counters[] = new Counter(
            new CounterId('8CE05088-ED1F-43E9-A415-3B3792655A9B'),
            new CounterName('twocounter'), new CounterValue(2), 'active',
            'passwordplaceholder'
        );
        $counters[] = new Counter(
            new CounterId('62A0CEB4-0403-4AA6-A6CD-1EE808AD4D23'),
            new CounterName('test'), new CounterValue(0), 'locked',
            'passwordplaceholder'
        );
        $counters[] = new Counter(
            new CounterId('62A0CEB4-4575-4AA6-FD76-1EE808AD4D23'),
            new CounterName('onecounter'), new CounterValue(1), 'disabled',
            'passwordplaceholder'
        );
    $this->beConstructedWith($counters);
}
    function its_used_to_test_removal_of_existing_counters_by_id(){
        $counterId = new CounterId('1CE05088-ED1F-43E9-A415-3B3792655A9B');
        $this->removeCounterById($counterId)->shouldReturn(TRUE);
        // TODO: next verify that the item was removed
        //$this->getCounterById($counterId)->willReturnException('not Found', '404');
    }
/*    function its_used_to_test_removal_of_counters_by_name(){
        $counterName = new CounterName('onecounter');
        $this->removeCounterByName($counterName)->willReturn(TRUE);
        $this->getCounterByName('onecounter')->willReturnException('not Found', '404');
    }*/
}
