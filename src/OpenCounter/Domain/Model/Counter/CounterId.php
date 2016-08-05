<?php

namespace OpenCounter\Domain\Model\Counter;
use Ramsey\Uuid\Uuid;

/**
 * Class CounterId
 * @package OpenCounter\Domain\Model\Counter
 */

class CounterId
{
  private $uuid;

  /**
   * Constructor.
   *
   * @param string $uuid The string of id
   */
  public function __construct($uuid = null)
  {
    $this->uuid = null === $uuid ? Uuid::uuid4()->toString() : $uuid;
  }


  /**
   * Gets the uuid.
   *
   * @return string
   */
  public function uuid()
  {
    return $this->uuid;
  }
  /**
   * Method that checks if the counter id given is equal to the current.
   *
   * @param OpenCounter\Domain\Model\Counter\CounterId $uuid The counter id
   *
   * @return bool
   */
  public function equals(CounterId $counterId)
  {
    return $this->uuid() === $counterId->uuid();
  }
  /**
   * Magic method that represent the class in string format.
   *
   * @return string
   */
  public function __toString()
  {
    return (string) $this->uuid();
  }
}
