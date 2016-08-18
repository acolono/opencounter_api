<?php
/**
 * Created by PhpStorm.
 * Counter: rosenstrauch
 * Date: 8/7/16
 * Time: 8:55 PM
 */

namespace OpenCounter\Infrastructure\Factory\Counter;

use OpenCounter\Domain\Model\Counter\Counter;
use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Model\Counter\CounterName;
use OpenCounter\Domain\Model\Counter\CounterValue;

/**
 * Class CounterFactory
 * @package OpenCounter\Infrastructure\Factory\Counter
 */
class CounterFactory
{
    /**
     * Build a new counter object.
     *
     * @param \OpenCounter\Domain\Model\Counter\CounterId $anId
     * @param \OpenCounter\Domain\Model\Counter\CounterName $anName
     * @param \OpenCounter\Domain\Model\Counter\CounterValue $aValue
     * @param $status
     * @param $aPassword
     *
     * @return \OpenCounter\Domain\Model\Counter\Counter
     */
    public function build(
      CounterId $anId,
      CounterName $anName,
      CounterValue $aValue,
      $status,
      $aPassword
    ) {
        return new Counter($anId, $anName, $aValue, $status, $aPassword);
    }
}