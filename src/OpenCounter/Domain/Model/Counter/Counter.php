<?php

namespace OpenCounter\Domain\Model\Counter;


/**
 * Counter entity.
 *
 * @SWG\Definition(
 *   required={"name"},
 *
 * @SWG\ExternalDocumentation(
 *     description="find more info here",
 *     url="https://swagger.io/about"
 *   )
 * )
 */
class Counter
{

  /**
   * The counter entity id
   *
   * @var string
   * @SWG\Property()
   */

  protected $id;
  /**
   * The counter entity name.
   *
   * @var string
   * @SWG\Property(example="onecounter")
   */

  public $name;
  /**
   * The counter entity password.
   *
   * @var string
   * @SWG\Property(example="examplepassword")
   */

  protected $password;
  /**
   * The counter entity value.
   *
   * @var integer
   * @SWG\Property(format="int32")
   */

  public $value;
  /**
   * The counter entity status.
   *
   * @var string
   * @SWG\Property(enum={"active","locked","disabled"})
   */

  public $status;

  /**
   * @param \OpenCounter\Domain\Model\Counter\CounterId $anId
   *
   * @param \OpenCounter\Domain\Model\Counter\CounterName $anName
   *
   * @SWG\Parameter(
   * parameter="CounterName",
   * description="name of counter to fetch",
   * in="path",
   * name="name",
   * required=true,
   * type="string",
   * default="onecounter"
   * )
   *
* @param \OpenCounter\Domain\Model\Counter\CounterValue $aValue
   * @param $password
   */
  public function __construct(CounterName $aName, CounterId $anId, CounterValue $aValue, $password) {
    //$this->state = State::active();
    $this->id = $anId->uuid();
    $this->name = $aName->name();
    $this->value = $aValue->value();

    $this->status = 'active';
    $this->password = $password;
//    $this->changePassword($aPassword);
    //https://github.com/benatespina/ddd-symfony/issues/1
//    DomainEventPublisher::instance()->publish(new UserRegistered($this->userId));
  }

  /**
   * Counter Id.
   *
   * @return string
   *   The counter ID
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Get Counter Name.
   *
   * @return string
   *   Name of the counter
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Get Counter Password.
   *
   * @return string
   *   The Password.
   */
  public function getPassword() {
    return $this->password;
  }

  /**
   * Get Counter Value.
   *
   * @return int
   *   the count
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * Counter Status.
   *
   * @return string
   *   The counter ID
   */
  public function getStatus() {
    return $this->status;
  }


  /**
   * Reset Counter.
   *
   * @return string
   *   The counter Value
   */
  public function reset() {
    $this->value = 0;

    return $this->value;
  }

  /**
   * Increment Value
   *
   * @return mixed
   * @throws \Exception
   */
  public function incrementValue()
  {
    if (! $this->status == 'locked') {
      $new_value = $this->value->increment();
      return $new_value;
    }
    else {
      throw new \Exception("cannot increment locked counter", 1, NULL);
    }
  }

  public function lock()
  {
    // Set status to locked logic
//      $this->status = 'locked';
//    $this->locked = true;

    if (!$this->couldBeLocked()) {
      throw new \Exception("Could not do this transition");
    }

    return $this->state = State::locked();
  }

  public function isLocked()
  {
        if ($this->status == 'locked') {
          return TRUE;
        }
////    return $this->locked;
//    return $this->state->isLocked();
  }
  private function couldBeLocked()
  {
    return !$this->isLocked();
  }

}
