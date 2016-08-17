<?php

namespace OpenCounter\Domain\Model\Counter;
/**
 * class CounterName
 *
 *
 * @SWG\Definition(
 *   required={"name"}
 * )
 * Class CounterName
 * @package OpenCounter\Domain\Model\Counter
 */
class CounterName
{
    /**
     * The counter name.
     *
     * @var string
     * @SWG\Property(example="onecounter")
     */

    private $name;

    /**
     * Constructor.
     *
     * This is a value object that takes a string and normalizes it.
     *
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * get name string out of the value object through this public function
     * @return string
     */
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
