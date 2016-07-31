<?php

namespace spec\OpenCounter\Infrastructure\Persistence\Sql;

use OpenCounter\Storage;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SqlManagerSpec extends ObjectBehavior
{
  function let()
  {
    $this->beConstructedWith('mysql:host=172.17.0.4;dbname=countapp', 'root', 'countapp');
  }
  function it_initializable()
  {
    $this->shouldHaveType('OpenCounter\Infrastructure\Persistence\Sql\SqlManager');
  }
  function its_connection()
  {
    $this->connection()->shouldReturnAnInstanceOf('PDO');
  }
  function it_executes()
  {
    $this->execute('SELECT * FROM tablename', null)->shouldReturnAnInstanceOf('PDOStatement');
  }
}
