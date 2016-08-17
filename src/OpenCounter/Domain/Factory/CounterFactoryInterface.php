<?php

namespace Domain\Factory\Counter;

use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Model\Counter\CounterName;

/**
 * Interface CounterFactoryInterface
 * @package Domain\Factory\Counter
 */
interface CounterFactoryInterface
{
  /**
   * Creation method that registers a new counter into domain.
   *
   * @param \Domain\Model\Counter\CounterId    $anId      The counter id
   * @param \Domain\Model\Counter\CounterValue $anValue   The counter value address
   * @param \Domain\Model\Counter\CounterValue $aName   The counter value address
   * @param string $aPassword The password
   * @param string $status status
   *
   * @return \Domain\Model\Counter\Counter
   */
  public function build(CounterId $anId, CounterName $aName, CounterValue $anValue, $status, $aPassword);
}