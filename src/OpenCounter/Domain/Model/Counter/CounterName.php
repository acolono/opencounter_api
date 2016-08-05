<?php

namespace OpenCounter\Domain\Model\Counter;
/**
 * Class CounterName
 * @package OpenCounter\Domain\Model\Counter
 */
class CounterName
{
    public function __construct($aName)
    {
        $this->name = $aName;
    }

    public function name()
    {
        return $this->name;
    }
  /**
   * Magic method that represent the class in string format.
   *
   * @return string
   */
  public function __toString()
  {
    return $this->name();
  }
}
