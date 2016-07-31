<?php

namespace OpenCounter\Domain\Model\Counter;

class Counter
{

  protected $anId;
  /**
   * The counter entity name.
   *
   * @var string
   * @SWG\Property()
   */
  public $name;
  /**
   * The counter entity password.
   *
   * @var string
   * @SWG\Property()
   */
  protected $password;
  /**
   * The counter entity value.
   *
   * @var integer
   * @SWG\Property()
   */
  public $value;
  /**
   * The counter entity status.
   *
   * @var integer
   * @SWG\Property()
   */
  public $status;

  /**
   * @param \OpenCounter\Domain\Model\Counter\CounterId $anId
   * @param \OpenCounter\CounterValue $value
   * @param $name
   * @param $password
   */
  public function __construct(CounterId $anId, CounterValue $value, $password) {
    //$this->state = State::active();
    if (isset($anId)) {
      $this->counterId = $anId;
    }
    if (isset($value)) {
      $this->value = $value;
    }
    $this->status = 'locked';
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
    return $this->counterId;
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
   *   The counter ID
   */
  public function reset() {
    $this->value = 0;

    return $this->value;
  }
  /**
   * Increment Value.
   *
   * @return string
   *   The counter ID
   */
  public function incrementValue()
  {
    if (! $this->status == 'locked') {
      $new_value = $this->value->incrementValue();
      return $new_value;
    }
    else {
      throw new \Exception("cannot increment locked counter", 1, NULL);
    }

  }

  public function lock()
  {
    // Set status to locked logic
      $this->status = 'locked';
//    $this->locked = true;
  }

  public function isLocked()
  {
        if ($this->status == 'locked') {
          return TRUE;
        }
//    return $this->locked;
  }

    public function id()
    {
        // TODO: write logic here
    }

    public function value()
    {
        // TODO: write logic here
    }

    public function password()
    {
        // TODO: write logic here
    }

    public function changePassword($argument1)
    {
        // TODO: write logic here
    }

    public function increment()
    {
        // TODO: write logic here
    }
}
