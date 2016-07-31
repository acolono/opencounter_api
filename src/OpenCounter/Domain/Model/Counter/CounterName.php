<?php

namespace OpenCounter\Domain\Model\Counter;

class CounterName
{
    public function __construct($aName)
    {
        $this->name = $aName;
    }

    public function getName()
    {
        return $this->name;
    }
}
