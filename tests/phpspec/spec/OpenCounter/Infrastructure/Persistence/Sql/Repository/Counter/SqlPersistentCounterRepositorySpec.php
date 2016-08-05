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
  function let(SqlManager $pdo)
  {
    $this->beConstructedWith($pdo);
  }
   function it_can_be_instanciated(){
     // make sure it extends SqlCounterRepository
     $this->shouldHaveType('OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlCounterRepository');
   }
   function it_persists_counters_into_database(){}
   function it_updates_counters_in_the_database(){}
   function it_probably_doesnt_create_new_counters(){}
}