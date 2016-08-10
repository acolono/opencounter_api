<?php

namespace spec\OpenCounter\Infrastructure\Persistence\Sql;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SqlManagerSpec extends ObjectBehavior
{
  const TABLE_NAME = 'counters';
  protected $db;

  function let(\PDO $db)
  {

//    $this->beConstructedWith('mysql:host=172.17.0.4;dbname=countapp', 'root', 'countapp');
    $this->beConstructedWith($db);
    $this->db = $db;

  }
  function it_initializable()
  {
    $this->shouldHaveType('OpenCounter\Infrastructure\Persistence\Sql\SqlManager');
    $this->shouldImplement('OpenCounter\Infrastructure\Persistence\StorageInterface');
  }
  function its_connection()
  {
    $this->connection()->shouldReturnAnInstanceOf('PDO');
  }
//  function it_executes_prepared_statements($db)
//  {
//
//    $statement = sprintf('SELECT * FROM %s', null, self::TABLE_NAME);
//    $this->prepare($statement)->willBeCalled()
//    $this->execute($statement)->willBeCalled()->shouldReturnAnInstanceOf('PDOStatement');
//
//  }
}
