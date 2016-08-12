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
   * @var string
   * @SWG\Property(example="+1")
   */
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
