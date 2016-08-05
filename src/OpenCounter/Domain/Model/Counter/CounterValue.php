<?php

namespace OpenCounter\Domain\Model\Counter;
/**
 * Class CounterValue
 *
 * @package OpenCounter\Domain\Model\Counter
 */
class CounterValue
{
private $value;

    public function __construct($value)
    {
      if (isset($value)) {
        $this->value = $value;
      }
      else {
        $this->value = 0;
      }

    }
    public function value()
    {
      return $this->value;
    }

    public function increment($increment)
    {
      $this->value = $this->value + $increment;
      return $this->value;
    }

}
