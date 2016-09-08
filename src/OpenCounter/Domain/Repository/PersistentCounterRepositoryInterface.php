<?php
namespace OpenCounter\Domain\Repository;

use OpenCounter\Domain\Model\Counter\Counter;
use OpenCounter\Domain\Model\Counter\CounterName;

/**
 * Persistent repository has a few more methods for saving.
 *
 * Interface PersistentCounterRepositoryInterface
 * @package OpenCounter\Domain\Repository
 */
interface PersistentCounterRepositoryInterface extends CounterRepositoryInterface
{
  /**
   * Saves the counter given.
   *
   * @param \OpenCounter\Domain\Model\Counter\Counter $anCounter
   * @return mixed
   */
  function save(Counter $anCounter);

  /**
   * @param \OpenCounter\Domain\Model\Counter\Counter $anCounter
   *
   * @return mixed
   */
  function insert(Counter $anCounter);

  /**
   * @param \OpenCounter\Domain\Model\Counter\Counter $anCounter
   *
   * @return mixed
   */
  function update(Counter $anCounter);

  /**
   * @param \OpenCounter\Domain\Model\Counter\CounterName $aName
   *
   * @return mixed
   */
  public function removeCounterByName(CounterName $aName);

}