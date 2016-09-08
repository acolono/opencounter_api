<?php
namespace OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter;

use OpenCounter\Domain\Model\Counter\Counter;
use OpenCounter\Domain\Model\Counter\CounterName;
use OpenCounter\Domain\Repository\PersistentCounterRepositoryInterface;
use OpenCounter\Infrastructure\Persistence\Sql\SqlManager;

/**
 * Class SqlPersistentCounterRepository
 * @package OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter
 */
class SqlPersistentCounterRepository extends SqlCounterRepository implements PersistentCounterRepositoryInterface
{
    /**
     * The manager
     *
     * @var \OpenCounter\Infrastructure\Persistence\Sql\SqlManager
     */
    protected $manager;

    /**
     * Constructor.
     *
     * Add prepared statements to be used by our methods.
     *
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
        $this->insertStmt = $this->manager->prepare(
          sprintf("INSERT INTO %s (name, uuid, value, status, password) VALUES (:name, :uuid, :value, :status, :password)",
            self::TABLE_NAME)
        );
        $this->updateStmt = $this->manager->prepare(
          sprintf('UPDATE %s SET value = :value, status = :status, password = :password WHERE uuid = :uuid',
            self::TABLE_NAME)
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
          sprintf('SELECT COUNT(*) FROM %s WHERE uuid = :uuid',
            self::TABLE_NAME),
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
          'status' => $anCounter->getStatus(),
          'password' => 'passwordplaceholder'
        ]);

    }
    public function removeCounterByName(CounterName $counterName) {
        // TODO: allow removing counters from db
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
          'status' => $anCounter->getStatus(),
          'password' => 'passwordplaceholder'
        ]);

    }

}