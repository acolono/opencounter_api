<?php
/**
 * Created by PhpStorm.
 * User: buddy
 * Date: 04/08/16
 * Time: 17:38
 */

namespace spec\OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter;

use OpenCounter\Infrastructure\Persistence\Sql\SqlManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SqlPersistentCounterRepositorySpec extends ObjectBehavior
{
  private $manager;
  const TABLE_NAME = 'counters';

  function let(SqlManager $manager)
  {
    // mocking sql manager
    $manager = new SqlManager(new \PDO(
      'mysql:host=database;dbname=countapp', 'root', 'countapp'

    ));

    $this->insertStmt = $manager->prepare(
      sprintf("INSERT INTO %s (name, uuid, value, password) VALUES (:name, :uuid, :value, :password)", self::TABLE_NAME)
    );
    $this->updateStmt = $manager->prepare(
      sprintf('UPDATE %s SET value = :value, password = :password WHERE uuid = :uuid', self::TABLE_NAME)
    );
    $this->beConstructedWith($manager);

  }
//   function it_can_be_instanciated(SqlManager $manager){
//
//     // make sure it extends SqlCounterRepository
//     $this->shouldHaveType('OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlCounterRepository');
//   }
//   function it_persists_counters_into_database(){}
//   function it_updates_counters_in_the_database(){}
//   function it_probably_doesnt_create_new_counters(){}
}