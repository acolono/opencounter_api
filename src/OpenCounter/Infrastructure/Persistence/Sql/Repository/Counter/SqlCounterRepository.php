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
    $this->db = $manager;
  }

  /**
   * {@inheritdoc}
   */
  public function remove(Counter $anCounter)
  {
    $this->db->execute(
      sprintf('DELETE FROM %s WHERE id = :id', self::TABLE_NAME), ['id' => $anCounter->getId()->id()]
    );
  }
  /**
   * {@inheritdoc}
   */
  public function counterOfId(CounterId $anId)
  {
    $statement = $this->db->execute(
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
    $statement = $this->db->execute(
      sprintf('SELECT * FROM %s WHERE value = :value', self::TABLE_NAME), ['value' => $anValue->value()]
    );
    if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
      return $this->buildCounter($row);
    }
    return;
  }
  /**
   * {@inheritdoc}
   */
  public function fetchCounterByName(CounterName $anName)
  {
    $statement = $this->db->execute(
      sprintf('SELECT * FROM %s WHERE name = :name', self::TABLE_NAME), ['name' => $anName->name()]
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
    $statement = $this->db->execute($sql, $parameters);
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
    return new Counter(new CounterName($row['name']), new CounterId($row['id']), new CounterValue('0'), $row['password']);
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
   * get a specific counter by id
   *
   * @param \OpenCounter\Domain\Model\Counter\CounterId $anId
   * @return \Domain\Model\Counter\Counter|void
   */
  public function getCounterById(CounterId $anId) {


    $statement = $this->db->execute(
      sprintf('SELECT * FROM %s WHERE id = :id', self::TABLE_NAME), ['id' => $anId->id()]
    );
    if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
      return $this->buildCounter($row);
    }
    return;

//    $sql = 'SELECT c.id, c.name, c.password, c.value
//            from counters c
//            where c.name = :name';
//    $stmt = $this->db->prepare($sql);
//    $result = $stmt->execute(['name' => $name]);
//
//    if($result && $data = $stmt->fetch()){
//      return new CounterEntity($data);
//    }
  }

  /**
   * get a specific counter by name
   *
   * @param \OpenCounter\Domain\Model\Counter\CounterName $anName
   * @return \OpenCounter\Domain\Model\Counter\Counter|void
   */

  public function getCounterByName(CounterName $anName) {


    $statement = $this->db->execute(
      sprintf('SELECT * FROM %s WHERE name = :name', self::TABLE_NAME), ['name' => $anName->name()]
    );
    if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
      return $this->buildCounter($row);
    }
    return;

//    $sql = 'SELECT c.id, c.name, c.password, c.value
//            from counters c
//            where c.name = :name';
//    $stmt = $this->db->prepare($sql);
//    $result = $stmt->execute(['name' => $name]);
//
//    if($result && $data = $stmt->fetch()){
//      return new CounterEntity($data);
//    }
  }

  /**
   * Get single counter by Credentials
   *
   * @param $name
   * @param $password
   * @return \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\CounterEntity
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
   * @param \OpenCounter\Domain\Model\Counter\Counter $counter
   * @return \Domain\Model\Counter\Counter|void
   */
  private function insert(Counter $counter) {



    $statement = $this->db->execute(
      sprintf('INSERT into %s  (name, password, value) values (:name, :password, :value)', self::TABLE_NAME), [
        'id' => $counter->getId(),
        'name' => $counter->getName(),
        'value' => $counter->getValue(),
      ]
    );
    if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
      return $this->buildCounter($row);
    }
    return;


//    $sql = 'INSERT into counters (name, password, value)
//            values (:name, :password, :value)';
//    $stmt = $this->db->prepare($sql);
//    $result = $stmt->execute([
//      'name' => $counterEntity->getName(),
//      'password' => $counterEntity->getPassword(),
//      'value' => $counterEntity->getValue(),
//    ]);
//
//    if(!$result) {
//      throw new Exception("could not save record");
//    }
  }

  /**
   * Reset Single Counter
   *
   * @param \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\CounterEntity $counterEntity
   */
  private function update(Counter $counter) {
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