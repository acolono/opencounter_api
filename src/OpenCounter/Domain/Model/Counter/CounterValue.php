<?php

namespace OpenCounter\Domain\Model\Counter;

class CounterValue
{
private $value;

    public function __construct($value)
    {
      $this->value = $value;
    }
    public function getValue()
    {
      return $this->value;
    }

    public function incrementValue()
    {
      $this->value = $this->value + 1;
      return $this->value;
    }


}
