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
  public function exist(Counter $anCounter)
  {

    return $this->db->execute(
      sprintf('SELECT COUNT(*) FROM %s WHERE uuid = :uuid', self::TABLE_NAME),
      [':uuid' => $anCounter->getId()]
    )->fetchColumn() == 1;
  }
  /**
   * Inserts the counter given into database.
   *
   * @param \OpenCounter\Domain\Model\Counter\Counter $anCounter The counter
   */
  public function insert(Counter $anCounter)
  {
    $this->db->execute(
      sprintf('INSERT INTO %s (name, uuid, value, password) VALUES (:name, :uuid, :value, :password)', self::TABLE_NAME),
      ['name' => $anCounter->getName(),'uuid' => $anCounter->getId(), 'value' => $anCounter->getValue(), 'password' => 'passwordplaceholder']
    );
  }
  /**
   * Updates the counter given into database.
   *
   * @param \OpenCounter\Domain\Model\Counter\Counter $anCounter The counter
   */
  public function update(Counter $anCounter)
  {
    $this->db->execute(
      sprintf('UPDATE %s SET value = :value, password = :password WHERE uuid = :uuid', self::TABLE_NAME),
      ['uuid' => $anCounter->getId(), 'value' => $anCounter->getValue(), 'password' => $anCounter->getPassword()]
    );
  }


}