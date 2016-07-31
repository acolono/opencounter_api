<?php

namespace OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter;
use OpenCounter\Domain\Model\Counter\Counter;
use OpenCounter\Domain\Model\Counter\CounterName;
use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Repository\Counter\CounterRepository;
use OpenCounter\Domain\Repository\CounterRepositoryInterface;
use OpenCounter\Infrastructure\Persistence\Sql\SqlManager;

/**
 * Class SqlCounterRepository
 * @package OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter
 */
class SqlCounterRepository implements CounterRepositoryInterface
{
  const TABLE_NAME = 'counters';
  /**
   * The manager.
   *
   * @var \Infrastructure\Persistence\Sql\SqlManager
   */
  protected $pdo;
  /**
   * Constructor.
   *
   * @param \Infrastructure\Persistence\Sql\SqlManager $manager The manager
   */
  public function __construct(SqlManager $manager)
  {
    $this->pdo = $manager;
  }
  /**
   * {@inheritdoc}
   */
  public function remove(Counter $anCounter)
  {
    $this->pdo->execute(
      sprintf('DELETE FROM %s WHERE id = :id', self::TABLE_NAME), ['id' => $anCounter->id()->id()]
    );
  }
  /**
   * {@inheritdoc}
   */
  public function counterOfId(CounterId $anId)
  {
    $statement = $this->pdo->execute(
      sprintf('SELECT * FROM %s WHERE id = :id', self::TABLE_NAME), ['id' => $anId->id()]
    );
    if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
      return $this->buildCounter($row);
    }
    return;
  }
  /**
   * {@inheritdoc}
   */
  public function counterOfValue(CounterValue $anValue)
  {
    $statement = $this->pdo->execute(
      sprintf('SELECT * FROM %s WHERE value = :value', self::TABLE_NAME), ['value' => $anValue->getValue()]
    );
    if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
      return $this->buildCounter($row);
    }
    return;
  }
  /**
   * {@inheritdoc}
   */
  public function counterOfName(CounterName $anName)
  {
    $statement = $this->pdo->execute(
      sprintf('SELECT * FROM %s WHERE name = :name', self::TABLE_NAME), ['name' => $anName->getName()]
    );
    if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
      return $this->buildCounter($row);
    }
    return;
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
      sprintf('SELECT * FROM %s WHERE %s', self::TABLE_NAME, $specification->toSqlClauses())
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
    return $this->pdo
      ->execute(sprintf('SELECT COUNT(*) FROM %s', self::TABLE_NAME))
      ->fetchColumn();
  }
  /**
   * Executes the sql given and returns the result in array of counters.
   *
   * @param string $sql        The sql query
   * @param array  $parameters Array which contains the parameters
   *
   * @return array
   */
  private function retrieveAll($sql, array $parameters = [])
  {
    $statement = $this->pdo->execute($sql, $parameters);
    return array_map(function ($row) {
      return $this->buildCounter($row);
    }, $statement->fetchAll(\PDO::FETCH_ASSOC));
  }
  /**
   * Builds an counter object with the given sql row in array format.
   *
   * @param array $row The sql row in array format
   *
   * @return \Domain\Model\Counter\Counter
   */
  private function buildCounter(array $row)
  {
    return new Counter(new CounterId($row['id']), new CounterValue($row['name']), $row['password']);
  }
}