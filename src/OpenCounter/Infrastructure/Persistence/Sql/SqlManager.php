<?php

namespace OpenCounter\Infrastructure\Persistence\Sql;


use OpenCounter\Infrastructure\Persistence\Storage;

class SqlManager implements Storage
{
  const SQL_DATE_FORMAT = 'Y-m-d H:i:s';
  /**
   * The pdo instance.
   *
   * @var \PDO
   */
  private $pdo;
  /**
   * Constructor.
   *
   * @param string $dsn      The dsn
   * @param string $username The db user
   * @param string $password The db password
   * @param array  $options  Array which contains more options
   */
  public function __construct($dsn, $username, $password, array $options = null)
  {
    $this->pdo = new \PDO($dsn, $username, $password, $options);
  }
  /**
   * Gets connection of database.
   *
   * @return \PDO
   */
  public function connection()
  {
    return $this->pdo;
  }
  /**
   * Executes the sql given with the parameters given.
   *
   * @param string $sql        The sql in string format
   * @param array  $parameters Array which contains parameters, it can be null
   *
   * @return \PDOStatement
   */
  public function execute($sql, array $parameters = null)
  {
    $statement = $this->pdo->prepare($sql);
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
      $this->pdo->commit();
      return $return ?: true;
    } catch (\Exception $exception) {
      $this->pdo->rollback();
      throw $exception;
    }
  }

  /**
   * @inheritDoc
   */
  public function persist($data) {
    // TODO: Implement persist() method.
  }

  /**
   * @inheritDoc
   */
  public function retrieve($id) {
    // TODO: Implement retrieve() method.
  }

  /**
   * @inheritDoc
   */
  public function delete($id) {
    // TODO: Implement delete() method.
  }

}