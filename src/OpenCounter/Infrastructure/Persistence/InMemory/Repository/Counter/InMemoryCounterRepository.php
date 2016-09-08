<?php
/**
 * Created by PhpStorm.
 * Counter: rosenstrauch
 * Date: 8/6/16
 * Time: 2:09 PM
 */

namespace OpenCounter\Infrastructure\Persistence\InMemory\Repository\Counter;

use OpenCounter\Domain\Model\Counter\Counter;
use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Model\Counter\CounterName;
use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Domain\Repository\CounterRepositoryInterface;

/**
 * Class InMemoryCounterRepository
 * @package OpenCounter\Infrastructure\Persistence\InMemory\Repository\Counter
 */
class InMemoryCounterRepository implements CounterRepositoryInterface
{
    /**
     * @var $counters
     */
    private $counters = [];

    /**
     * Constructor
     *
     * create a few counters we can use during tests
     */
    public function __construct(array $counters)
    {
        foreach ($counters as $item) {
            $this->add($item);
        }


    }
    public function exists(CounterId $counterId){
        $id = (string) $counterId->uuid();
        return array_key_exists($id, $this->counters);
    }
    /**
     * @inheritDoc
     */
    public function find(CounterId $counterId)
    {
    }
    /**
     * @inheritDoc
     */
    public function findAll()
    {
        return $this->counters;
    }
    /**
     * @inheritDoc
     */
    public function add(Counter $counter)
    {
        $id = (string) $counter->getId();
        $this->counters[$id] = clone $counter;
        return clone $counter;
    }
    /**
     * @inheritDoc
     */
    public function remove(Counter $counter)
    {
        unset($this->counters[$counter->getId()]);
    }

    /**
     * @inheritDoc
     */
    public function getCounterById(CounterId $anId)
    {
        // TODO: Implement getCounterById() method.
    }

    /**
     * @inheritDoc
     */
    public function getCounterByName(CounterName $aName)
    {
        // TODO: Implement getCounterByName() method.
    }

    /**
     * @inheritDoc
     */
    public function getCounterByUuid(CounterId $anId)
    {
        unset($this->counters[$anId]);
    }

    /**
     * @inheritDoc
     */
    public function query($specification)
    {
        // TODO: Implement query() method.
    }

    /**
     * @inheritDoc
     */

    public function nextIdentity()
    {
        return new CounterId();
    }

    /**
     * @inheritDoc
     */
    public function size()
    {
        // TODO: Implement size() method.
    }

    /**
     * @inheritDoc
     */
    public function counterOfId(CounterId $anId)
    {
        // TODO: Implement counterOfId() method.
    }

    public function removeCounterByName(CounterName $aName)
    {
print_r($this->counters);
        unset($this->counters[$aName]);
    }
    public function removeCounterById(CounterId $anId)
    {
        if ($this->exists($anId)){
          unset($this->counters[$anId->uuid()]);
            // we are lazy and returning true because we tried removing,
            // TODO: where do we deal with removal of nonexisting counters
            return TRUE;
         }
    }

}