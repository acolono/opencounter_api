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
  protected $manager;

  public function __construct(SqlManager $manager)
  {
    $this->manager = $manager;
    $this->insertStmt = $this->manager->prepare(
        sprintf("INSERT INTO %s (name, uuid, value, password) VALUES (:name, :uuid, :value, :password)", self::TABLE_NAME)
    );
    $this->updateStmt = $this->manager->prepare(
        sprintf('UPDATE %s SET value = :value, password = :password WHERE uuid = :uuid', self::TABLE_NAME)
    );

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

    return $this->manager->execute(
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
    $insert = $this->insertStmt->execute([
        'name' => $anCounter->getName(),
        'uuid' => $anCounter->getId(),
        'value' => $anCounter->getValue(),
        'password' => 'passwordplaceholder']);

  }
  /**
   * Updates the counter given into database.
   *
   * @param \OpenCounter\Domain\Model\Counter\Counter $anCounter The counter
   */
  public function update(Counter $anCounter)
  {
    $update = $this->updateStmt->execute([
        'uuid' => $anCounter->getId(),
        'value' => $anCounter->getValue(),
        'password' => 'passwordplaceholder'
    ]);

  }

}