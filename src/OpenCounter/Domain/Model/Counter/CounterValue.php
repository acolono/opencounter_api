<?php

namespace OpenCounter\Domain\Model\Counter;
/**
 * Class CounterValue.
 *
 * @SWG\Definition(
 * @SWG\ExternalDocumentation(
 *     description="find more info here",
 *     url="https://swagger.io/about"
 *   )
 * )
 * @package OpenCounter\Domain\Model\Counter
 */
class CounterValue
{

  /**
   * The counter value.
   *
   * @var int
   * @SWG\Property(example="+1")
   */
private $value;

    /**
     * Constructor.
     *
     * Takes an integer and defaults to 0
     * @param $value
     */
    public function __construct($value)
    {
      if (isset($value)) {
        $this->value = $value;
      }
      else {
        $this->value = 0;
      }

    }

    /**
     * Return Value integer property from CounterValue Object
     * @return int
     */
    public function value()
    {
      return (int) $this->value;
    }

}
