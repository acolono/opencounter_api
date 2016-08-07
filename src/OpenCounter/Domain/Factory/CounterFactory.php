<?php

namespace Domain\Factory\Counter;
use Domain\Model\Counter\CounterValue;
use Domain\Model\Counter\CounterId;
/**
 * Interface CounterFactory.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
interface CounterFactory
{
  /**
   * Creation method that registers a new counter into domain.
   *
   * @param \Domain\Model\Counter\CounterId    $anId      The counter id
   * @param \Domain\Model\Counter\CounterValue $anValue   The counter value address
   * @param string                       $aPassword The password
   *
   * @return \Domain\Model\Counter\Counter
   */
  public function register(CounterId $anId, CounterValue $anValue, $aPassword);
}