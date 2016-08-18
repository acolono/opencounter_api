<?php

namespace OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter;

use OpenCounter\Domain\Model\Counter\Counter;
use OpenCounter\Domain\Model\Counter\CounterName;
use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Repository\CounterRepositoryInterface;
use OpenCounter\Infrastructure\Persistence\Sql\SqlManager;

/**
 * Class SqlCounterRepository
 * @package OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter
 */
class SqlCounterRepository implements CounterRepositoryInterface
{
    const TABLE_NAME = 'counters';


    protected $manager;

    /**
     * @param \OpenCounter\Infrastructure\Persistence\Sql\SqlManager $manager
     */
    public function __construct(SqlManager $manager)
    {
        $this->manager = $manager;
        $this->removeStmt = $this->manager->prepare(
          sprintf('DELETE FROM %s WHERE uuid = :uuid', self::TABLE_NAME)
        );
        $this->getStmt = $this->manager->prepare(
          sprintf('SELECT * FROM %s WHERE name = :name', self::TABLE_NAME)
        );


    }

    /**
     * {@inheritdoc}
     */
    public function remove(Counter $anCounter)
    {
        $this->removeStmt->execute(['uuid' => $anCounter->getId()]);
    }


    /**
     * {@inheritdoc}
     */
    public function query($specification)
    {
        if (!$specification instanceof SqlCounterSpecification) {
            throw new \InvalidArgumentException('This argument must be a SQLCounterSpecification');
        }
        return $this->retrieveAll(
          sprintf('SELECT * FROM %s WHERE %s', self::TABLE_NAME,
            $specification->toSqlClauses())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function nextIdentity()
    {
        return new CounterId();
    }

    /**
     * {@inheritdoc}
     */
    public function size()
    {
        return $this->manager
          ->execute(sprintf('SELECT COUNT(*) FROM %s', self::TABLE_NAME))
          ->fetchColumn();
    }

    /**
     * Executes the sql given and returns the result in array of counters.
     *
     * @param string $sql The sql query
     * @param array $parameters Array which contains the parameters
     *
     * @return array
     */
    private function retrieveAll($sql, array $parameters = [])
    {
        $statement = $this->manager->execute($sql, $parameters);
        return array_map(function ($row) {
            return $this->buildCounter($row);
        }, $statement->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * Builds an counter object with the given sql row in array format.
     *
     * @param array $row The sql row in array format
     *
     * @return \OpenCounter\Domain\Model\Counter\Counter
     */
    private function buildCounter(array $row)
    {
        // debug
//    print_r($row);
        return new Counter(
          new CounterId($row['uuid']),
          new CounterName($row['name']),
          new CounterValue($row['value']),
          $row['status'],
          $row['password']
        );
    }

    /**
     * Get a list of all counters
     *
     * @return array
     */
    public function getCounters()
    {
        $sql = 'SELECT c.uuid, c.name, c.password, c.value
            from counters c';
        $stmt = $this->manager->query($sql);

        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = new CounterEntity($row);
        }

        return $results;
    }

    /**
     * Get a specific counter by id.
     *
     * @param \OpenCounter\Domain\Model\Counter\CounterId $anId
     *
     * @return \OpenCounter\Domain\Model\Counter\Counter
     */

    public function getCounterById(CounterId $anId)
    {
        $statement = $this->manager->execute(
          sprintf('SELECT * FROM %s WHERE uuid = :uuid', self::TABLE_NAME),
          ['uuid' => $anId->uuid()]
        );
        if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            return $this->buildCounter($row);
        }

    }

    /**
     * get a specific counter by uuid.
     *
     * @param \OpenCounter\Domain\Model\Counter\CounterId $anId
     *
     * @return \OpenCounter\Domain\Model\Counter\Counter
     */
    public function getCounterByUuid(CounterId $anId)
    {

        $statement = $this->manager->execute(
          sprintf('SELECT * FROM %s WHERE uuid = :uuid', self::TABLE_NAME),
          ['uuid' => $anId->uuid()]
        );
        if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            return $this->buildCounter($row);
        }

    }

    /**
     * Get a specific counter by name.
     *
     * @param \OpenCounter\Domain\Model\Counter\CounterName $aName
     *
     * @return \OpenCounter\Domain\Model\Counter\Counter
     */

    public function getCounterByName(CounterName $aName)
    {
        $statement = $this->manager->execute(
          sprintf('SELECT * FROM %s WHERE name = :name', self::TABLE_NAME),
          ['name' => $aName->name()]
        );
        if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            return $this->buildCounter($row);
        }
    }

    /**
     * Get single counter by Credentials
     *
     * @param $name
     * @param $password
     *
     * @return \OpenCounter\Domain\Model\Counter\Counter
     */

    public function getCounterByCredentials($name, $password)
    {
        $sql = 'SELECT c.uuid, c.name, c.password, c.value
            from counters c
            where c.name = :name and c.password = :password';
        $stmt = $this->manager->prepare($sql);
        $result = $stmt->execute(
          [
            'name' => $name,
            'password' => $password
          ]
        );

        if ($result && $data = $stmt->fetch()) {
            return new Counter($data);
        }
    }
}