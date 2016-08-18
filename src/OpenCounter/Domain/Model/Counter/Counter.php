<?php

namespace OpenCounter\Domain\Model\Counter;

use OpenCounter\Domain\Exception\Counter\CounterLockedException;


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

    private $name;
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

    private $value;
    /**
     * The counter entity status.
     *
     * @var string
     * @SWG\Property(enum={"active","locked","disabled"})
     */

    private $status;

    /**
     * @param \OpenCounter\Domain\Model\Counter\CounterId $id
     * @param \OpenCounter\Domain\Model\Counter\CounterName $name
     * @param \OpenCounter\Domain\Model\Counter\CounterValue $value
     * @param $status
     * @param $password
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
     */
    public function __construct(
      CounterId $CounterId,
      CounterName $CounterName,
      CounterValue $CounterValue,
      $aStatus,
      $aPassword
    ) {
        $this->id = $CounterId->uuid();
        $this->name = $CounterName->name();
        $this->value = $CounterValue->value();

        $this->status = $aStatus;
        $this->password = $aPassword;
//    $this->changePassword($aPassword);
        //https://github.com/benatespina/ddd-symfony/issues/1
//    DomainEventPublisher::instance()->publish(new UserRegistered($this->userId));
    }

    /**
     * our equivalent of render, since properties are private we can use this method to render counter as array for display - this for now resembles the output model of a counter
     * @return array
     */
    public function toArray(){
        $counterArray = [
            'name' => $this->name,
            'value' => $this->value
        ];
        return $counterArray;
    }

    /**
     * Counter Id.
     *
     * @return string
     *   The counter ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Counter Name.
     *
     * @return string
     *   Name of the counter
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get Counter Password.
     *
     * @return string
     *   The Password.
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get Counter Value.
     *
     * @return int
     *   the count
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Counter Status.
     *
     * @return string
     *   The counter ID
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Reset Counter.
     *
     * @return string
     *   The counter Value
     */
    public function resetValueTo(CounterValue $counterValue)
    {
        $this->value = $counterValue->value();

        return $this->value;
    }

    /**
     * Increase Count
     *
     * @param \OpenCounter\Domain\Model\Counter\CounterValue $increment
     *
     * @return bool
     * @throws \OpenCounter\Domain\Exception\Counter\CounterLockedException
     */

    public function increaseCount(CounterValue $increment)
    {
        if (!$this->isLocked()) {
            $newValue = new CounterValue($this->getValue() + $increment->value());
            $this->value = $newValue->value();
            return true;
        } else {
            throw new CounterLockedException("cannot increment locked counter",
              1, null);
        }

    }

    /**
     * Lock Counter
     *
     * @return string
     * @throws \Exception
     */
    public function lock()
    {
        if (!$this->couldBeLocked()) {
            throw new \Exception("Could not set status to locked");
        }
        $this->status = 'locked';
        return $this->getStatus();
    }

    /**
     * Enable Counter.
     *
     * if it is not active
     *
     * @return string
     * @throws \Exception
     */
    public function enable()
    {
        if (!$this->couldBeLocked()) {
            throw new \Exception("Could not set active");
        }
        $this->status = 'active';
        return $this->getStatus();
    }

    /**
     * Check whether Counter is Locked
     *
     * @return bool
     */
    public function isLocked()
    {
        return ($this->status == 'locked');
    }

    /**
     * Check whether Counter can be locked
     * @return bool
     */
    private function couldBeLocked()
    {
        return !$this->isLocked();
    }

    public function changeNameTo(CounterName $newCounterName)
    {
        // TODO: write logic here
    }

}
