<?php

namespace OpenCounter\Infrastructure\Persistence\Sql;

use OpenCounter\Infrastructure\Persistence\StorageInterface;

/**
 * Class SqlManager
 * @package OpenCounter\Infrastructure\Persistence\Sql
 */
class SqlManager implements StorageInterface
{
    const SQL_DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var \PDO
     */
    protected $db;

    /**
     * @param \PDO $db
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Gets connection of database.
     *
     * @return \PDO
     */
    public function connection()
    {
        return $this->db;
    }

    /**
     * @param $sql
     *
     * @return \PDOStatement
     */
    public function prepare($sql)
    {
        $statement = $this->db->prepare($sql);
        return $statement;
    }

    /**
     * Executes the sql given with the parameters given.
     *
     * @param string $sql The sql in string format
     * @param array $parameters Array which contains parameters, it can be null
     *
     * @return \PDOStatement
     */
    public function execute($sql, array $parameters = null)
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($parameters);
        return $statement;
    }

    /**
     * Executes a function in a transaction.
     *
     * @param callable $callable The function to execute transactionally
     *
     * @return mixed The non-empty value returned from the closure or true instead.
     *
     * @throws \Exception during execution of the function or transaction commit,
     *                    the transaction is rolled back and the exception re-thrown
     */
    public function transactional(callable $callable)
    {
        $this->pdo->beginTransaction();
        try {
            $return = call_user_func($callable, $this);
            $this->db->commit();
            return $return ?: true;
        } catch (\Exception $exception) {
            $this->db->rollback();
            throw $exception;
        }
    }

    public function persist($data)
    {
    }

    public function retrieve($id)
    {
    }

    public function delete($id)
    {
    }
}