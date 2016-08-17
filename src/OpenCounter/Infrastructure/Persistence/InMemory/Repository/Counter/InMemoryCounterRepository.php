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
    private $counters;

    /**
     * Constructor
     *
     * create a few counters we can use during tests
     */
    public function __construct()
    {
        $this->counters[] = new Counter(
          new CounterId('8CE05088-ED1F-43E9-A415-3B3792655A9B'),
          new CounterName('twocounter'), new CounterValue(2), 'active',
          'passwordplaceholder'
        );
        $this->counters[] = new Counter(
          new CounterId('62A0CEB4-0403-4AA6-A6CD-1EE808AD4D23'),
          new CounterName('test'), new CounterValue(0), 'locked',
          'passwordplaceholder'
        );
        $this->counters[] = new Counter(
          new CounterId('62A0CEB4-4575-4AA6-FD76-1EE808AD4D23'),
          new CounterName('onecounter'), new CounterValue(1), 'disabled',
          'passwordplaceholder'
        );
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
    }
    /**
     * @inheritDoc
     */
    public function remove(Counter $counter)
    {
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
        // TODO: Implement getCounterByUuid() method.
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

}