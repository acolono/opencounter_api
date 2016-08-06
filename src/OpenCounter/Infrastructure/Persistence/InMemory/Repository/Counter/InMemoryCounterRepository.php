<?php
/**
 * Created by PhpStorm.
 * Counter: rosenstrauch
 * Date: 8/6/16
 * Time: 2:09 PM
 */

namespace OpenCounter\Infrastructure\Persistence\InMemory\Repository\Counter;

use OpenCounter\Domain\Model\Counter\Counter;
use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Repository\CounterRepositoryInterface;

class InMemoryCounterRepository implements CounterRepositoryInterface {

  private $counters;

  public function __construct()
  {
    $this->counters[] = new Counter(
      new CounterId('8CE05088-ED1F-43E9-A415-3B3792655A9B'), 'John', 'Doe'
    );
    $this->counters[] = new Counter(
      new CounterId('62A0CEB4-0403-4AA6-A6CD-1EE808AD4D23'), 'Jean', 'Bon'
    );
  }

  public function find(CounterId $counterId)
  {
  }

  public function findAll()
  {
    return $this->counters;
  }

  public function add(Counter $counter)
  {
  }

  public function remove(Counter $counter)
  {
  }
}