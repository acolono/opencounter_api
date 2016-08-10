<?php

namespace spec\OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter;

use OpenCounter\Domain\Model\Counter\Counter;
use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Model\Counter\CounterName;
use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Domain\Repository\CounterRepositoryInterface;
use OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlCounterRepository;

use OpenCounter\Infrastructure\Persistence\Sql\SqlManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Ramsey\Uuid\Uuid;

class SqlCounterRepositorySpec extends ObjectBehavior
{
  function let()
  {
    $this->counter_mapper = $counter_mapper;
    $this->beConstructedWith($this->counter_mapper);


  }
  function it_removes_the_counter_given(SqlManager $counter_mapper, \PDOStatement $statement, Counter $counter, CounterId $counterId)
  {
    $counter->getId()->shouldBeCalled()->willReturn('testuuid');
//    $counter->getId()->shouldBeCalled()->willReturn($counterId);
//    $counterId->uuid()->shouldBeCalled()->willReturn('testuuid');
    $pdo->execute(
      sprintf('DELETE FROM %s WHERE uuid = :uuid', SqlCounterRepository::TABLE_NAME), ['uuid' => 'testuuid']
    )->shouldBeCalled()->willReturn($statement);
    $this->remove($counter);

  }

  function it_cannot_return_counters_by_name_if_none_exist(SqlManager $pdo, \PDOStatement $statement, CounterName $name)
  {
    $name->name()->shouldBeCalled()->willReturn('onecounter');
    $pdo->execute(
      sprintf('SELECT * FROM %s WHERE name = :name', SqlCounterRepository::TABLE_NAME),
      ['name' => 'onecounter']
    )->shouldBeCalled()->willReturn($statement);
    $statement->fetch(\PDO::FETCH_ASSOC)->shouldBeCalled()->willReturn(0);
    $this->getCounterByName($name)->shouldReturn(NULL);
  }

  function it_can_get_counters_by_their_name(SqlManager $pdo, \PDOStatement $statement, CounterName $name)
  {
    $name->name()->shouldBeCalled()->willReturn('onecounter');
    $pdo->execute(
      sprintf('SELECT * FROM %s WHERE name = :name', SqlCounterRepository::TABLE_NAME),
      ['name' => 'onecounter']
    )->shouldBeCalled()->willReturn($statement);
    $statement->fetch(\PDO::FETCH_ASSOC)->shouldBeCalled()->willReturn(
      ['uuid' => 'testuuid', 'name' => 'onecounter', 'password' => 'password', 'value' => 1]
    );
    $this->getCounterByName($name)->shouldReturnAnInstanceOf('OpenCounter\Domain\Model\Counter\Counter');
  }
  //  function it_returns_id_if_counter_doesnt_exist(SqlManager $pdo, \PDOStatement $statement, CounterId $counterId)
//  {
//    $counterId->uuid()->shouldBeCalled()->willReturn('testuuid');
//    $pdo->execute(
//      sprintf('SELECT * FROM %s WHERE id = :id', SqlCounterRepository::TABLE_NAME), ['id' => 'testuuid']
//    )->shouldBeCalled()->willReturn($statement);
//    $statement->fetch(\PDO::FETCH_ASSOC)->shouldBeCalled()->willReturn(0);
//    $this->getCounterById($counterId)->shouldReturn(null);
//  }
//  function it_will_return_counter_object_of_id(SqlManager $pdo, \PDOStatement $statement, CounterId $counterId)
//  {
////    $counter->getId()->shouldBeCalled()->willReturn($counterId);
//    $counterId->uuid()->shouldBeCalled()->willReturn('testuuid');
//    $pdo->execute(
//      sprintf('SELECT * FROM %s WHERE id = :uuid', SqlCounterRepository::TABLE_NAME), ['uuid' => 'testuuid']
//    )->shouldBeCalled()->willReturn($statement);
//    $statement->fetch(\PDO::FETCH_ASSOC)->shouldBeCalled()->willReturn(
//      ['uuid' => 'testuuid', 'value' => '1', 'password' => 'password', 'status' => 'active', 'name' => 'onecounter']
//    );
//    $this->getCounterById($counterId)->shouldReturnAnInstanceOf('OpenCounter\Domain\Model\Counter\Counter');
//  }

//  function it_cannot_return_counters_by_value_if_none_exist(SqlManager $pdo, \PDOStatement $statement, CounterValue $counter_value)
//  {
//    $counter_value->getValue()->shouldBeCalled()->willReturn('1');
//    $pdo->execute(
//      sprintf('SELECT * FROM %s WHERE counter_value = :counter_value', SqlCounterRepository::TABLE_NAME),
//      ['counter_value' => '1']
//    )->shouldBeCalled()->willReturn($statement);
//    $statement->fetch(\PDO::FETCH_ASSOC)->shouldBeCalled()->willReturn(0);
//    $this->counterOfValue($counter_value)->shouldReturn(null);
//  }
//  function it_can_get_counters_by_their_value(SqlManager $pdo, \PDOStatement $statement, CounterValue $counter_value)
//  {
//    $counter_value->getValue()->shouldBeCalled()->willReturn(1);
//    $pdo->execute(
//      sprintf('SELECT * FROM %s WHERE counter_value = :counter_value', SqlCounterRepository::TABLE_NAME),
//      ['counter_value' => '1']
//    )->shouldBeCalled()->willReturn($statement);
//    $statement->fetch(\PDO::FETCH_ASSOC)->shouldBeCalled()->willReturn(
//      ['id' => 'testuuid', 'counter_value' => '1', 'password' => 'password']
//    );
//    $this->counterOfValue($counter_value)->shouldReturnAnInstanceOf('Domain\Model\Counter\Counter');
//  }
//  function its_query_when_the_specification_is_not_a_sql_counter_specification()
//  {
//    $this->shouldThrow(new \InvalidArgumentException('This argument must be a SQLCounterSpecification'))
//      ->during('query', [Argument::not('Infrastructure\Persistence\Sql\Repository\Counter\SqlCounterSpecification')]);
//  }

//
//  function its_queries_persistent_storage(SqlManager $pdo, \PDOStatement $statement, SqlCounterSpecification $specification)
//  {
//    $specification->toSqlClauses()->shouldBeCalled()->willReturn('1 = 1');
//    $pdo->execute(
//      sprintf('SELECT * FROM %s WHERE %s', SqlCounterRepository::TABLE_NAME, '1 = 1'), []
//    )->shouldBeCalled()->willReturn($statement);
//    $statement->fetchAll(\PDO::FETCH_ASSOC)->shouldBeCalled()->willReturn(
//      [['id' => 'testuuid', 'counter_value' => '1', 'password' => 'password']]
//    );
//    $this->query($specification);
//  }
  function its_next_identity()
  {
    $this->nextIdentity()->shouldReturnAnInstanceOf('\OpenCounter\Domain\Model\Counter\CounterId');
  }
//  function its_size(SqlManager $pdo, \PDOStatement $statement, SqlCounterSpecification $specification)
//  {
//    $pdo->execute(
//      sprintf('SELECT COUNT(*) FROM %s', SqlCounterRepository::TABLE_NAME)
//    )->shouldBeCalled()->willReturn($statement);
//    $statement->fetchColumn()->shouldBeCalled()->willReturn(2);
//    $this->size()->shouldReturn(2);
//  }
}
