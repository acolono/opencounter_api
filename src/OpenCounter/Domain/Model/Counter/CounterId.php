<?php

namespace OpenCounter\Domain\Model\Counter;


class CounterId
{
  private $id;

  /**
   * Constructor.
   *
   * @param string $anId The string of id
   */
  public function __construct($anId = null)
  {
    $this->id = null === $anId ? Uuid::uuid4()->toString() : $anId;
  }


  /**
   * Gets the id.
   *
   * @return string
   */
  public function id()
  {
    return $this->id;
  }
  /**
   * Method that checks if the counter id given is equal to the current.
   *
   * @param OpenCounter\Domain\Model\Counter\CounterId $anId The counter id
   *
   * @return bool
   */
  public function equals(CounterId $anId)
  {
    return $this->id() === $anId->id();
  }
  /**
   * Magic method that represent the class in string format.
   *
   * @return string
   */
  public function __toString()
  {
    return $this->id();
  }
}
