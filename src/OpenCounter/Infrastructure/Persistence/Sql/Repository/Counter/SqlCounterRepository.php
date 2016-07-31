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

  /**
   * Get a list of all counters
   *
   * @return array
   */
  public function getCounters() {
    $sql = 'SELECT c.id, c.name, c.password, c.value
            from counters c';
    $stmt = $this->db->query($sql);

    $results = [];
    while($row = $stmt->fetch()){
      $results[] = new CounterEntity($row);
    }

    return $results;
  }

  /**
   * get a specific counter by name.
   *
   * @param $name
   * @return \CounterEntity
   */
  public function getCounterByName($name) {
    $sql = 'SELECT c.id, c.name, c.password, c.value
            from counters c
            where c.name = :name';
    $stmt = $this->db->prepare($sql);
    $result = $stmt->execute(['name' => $name]);

    if($result && $data = $stmt->fetch()){
      return new CounterEntity($data);
    }
  }

  /**
   * Get single counter by Credentials
   *
   * @param $name
   * @param $password
   * @return \CounterEntity
   */
  public function getCounterByCredentials($name, $password) {
    $sql = 'SELECT c.id, c.name, c.password, c.value
            from counters c
            where c.name = :name and c.password = :password';
    $stmt = $this->db->prepare($sql);
    $result = $stmt->execute(
      [
        'name' => $name,
        'password' => $password
      ]
    );

    if($result && $data = $stmt->fetch()){
      return new CounterEntity($data);
    }
  }
  public function addCounter(CounterEntity $counterEntity)
  {
    $this->counters->add($counterEntity);
  }
  /**
   * Create new counter
   *
   * @param \CounterEntity $counterEntity
   * @throws \Exception
   */
  public function insert(CounterEntity $counterEntity) {
    $sql = 'INSERT into counters (name, password, value)
            values (:name, :password, :value)';
    $stmt = $this->db->prepare($sql);
    $result = $stmt->execute([
      'name' => $counterEntity->getName(),
      'password' => $counterEntity->getPassword(),
      'value' => $counterEntity->getValue(),
    ]);

    if(!$result) {
      throw new Exception("could not save record");
    }
  }

  /**
   * Reset Single Counter
   * @param \CounterEntity $counterEntity
   */
  public function update(CounterEntity $counterEntity) {
    $sql = 'UPDATE counters c
            set c.name = :name, c.password = :password, c.value = :value
            where c.id = :id';

    $stmt = $this->db->prepare($sql);
    $result = $stmt->execute([
      'id' => $counterEntity->getId(),
      'name' => $counterEntity->getName(),
      'password' => $counterEntity->getPassword(),
      'value' => $counterEntity->getValue(),
    ]);
  }
}