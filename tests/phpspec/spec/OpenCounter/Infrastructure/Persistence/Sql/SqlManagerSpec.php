<?php

namespace spec\OpenCounter\Infrastructure\Persistence\Sql;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SqlManagerSpec extends ObjectBehavior
{
  function let(\PDO $db)
  {
//    $this->beConstructedWith('mysql:host=172.17.0.4;dbname=countapp', 'root', 'countapp');
    $this->beConstructedWith($db);

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
//  function it_executes()
//  {
//    $this->execute('SELECT * FROM tablename', null)->shouldReturnAnInstanceOf('PDOStatement');
//  }
}
