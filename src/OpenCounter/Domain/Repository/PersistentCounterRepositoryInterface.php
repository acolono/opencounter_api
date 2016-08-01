<?php
namespace OpenCounter\Domain\Repository;

use OpenCounter\Domain\Model\Counter\Counter;

interface PersistentCounterRepositoryInterface extends CounterRepositoryInterface
{
  /**
   * Saves the counter given.
   *
   * @param \OpenCounter\Domain\Model\Counter\Counter $anCounter
   * @return mixed
   */

  public function save(Counter $anCounter);
}