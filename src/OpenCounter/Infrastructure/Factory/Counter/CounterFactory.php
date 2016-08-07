<?php
/**
 * Created by PhpStorm.
 * Counter: rosenstrauch
 * Date: 8/7/16
 * Time: 8:55 PM
 */

namespace OpenCounter\Infrastructure\Factory\Counter;


use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Model\Counter\CounterName;
use OpenCounter\Domain\Model\Counter\CounterValue;

class CounterFactory {
  /**
   * {@inheritdoc}
   */
  public function register(CounterId $anId, CounterName $anName, CounterValue $aValue, $aPassword)
  {
    return new Counter($anId, $anName, $aValue, $aPassword);
  }
}