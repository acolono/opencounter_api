<?php
namespace OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter;


use OpenCounter\Domain\Model\Counter\Counter;
use OpenCounter\Domain\Repository\PersistentCounterRepositoryInterface;
use OpenCounter\Infrastructure\Persistence\Sql\SqlManager;

class SqlPersistentCounterRepository extends SqlCounterRepository implements PersistentCounterRepositoryInterface
{
  /**
   * The manager.
   *
   * @var \Infrastructure\Persistence\Sql\SqlManager
   */
  protected $db;

  public function __construct(SqlManager $manager)
  {
    $this->db = $manager;
  }
  /**
   * {@inheritdoc}
   */
  public function save(Counter $anCounter)
  {
    $this->exist($anCounter) ? $this->update($anCounter) : $this->insert($anCounter);
  }
  /**
   * Checks that the counter given exists into database.
   *
   * @param \OpenCounter\Domain\Model\Counter\Counter $anCounter The counter
   *
   * @return bool
   */
  private function exist(Counter $anCounter)
  {

    return $this->db->execute(
      sprintf('SELECT COUNT(*) FROM %s WHERE id = :id', self::TABLE_NAME),
      [':id' => $anCounter->getId()]
    )->fetchColumn() == 1;
  }
  /**
   * Inserts the counter given into database.
   *
   * @param \OpenCounter\Domain\Model\Counter\Counter $anCounter The counter
   */
  private function insert(Counter $anCounter)
  {
    $this->db->execute(
      sprintf('INSERT INTO %s (name, id, value, password) VALUES (:name, :id, :value, :password)', self::TABLE_NAME),
      ['name' => $anCounter->getName()->name(),'id' => $anCounter->getId()->id(), 'value' => $anCounter->getValue()->value(), 'password' => 'passwordplaceholder']
    );
  }
  /**
   * Updates the counter given into database.
   *
   * @param \OpenCounter\Domain\Model\Counter\Counter $anCounter The counter
   */
  private function update(Counter $anCounter)
  {
    $this->db->execute(
      sprintf('UPDATE %s SET value = :value, password = :password WHERE id = :id', self::TABLE_NAME),
      ['id' => $anCounter->id()->id(), 'value' => $anCounter->value()->getValue(), 'password' => $anCounter->password()]
    );
  }
}