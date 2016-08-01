<?php

namespace OpenCounter\Domain\Model\Counter;

class CounterValue
{
private $value;

    public function __construct($value)
    {
      $this->value = $value;
    }
    public function value()
    {
      return $this->value;
    }

    public function increment()
    {
      $this->value = $this->value + 1;
      return $this->value;
    }

}
