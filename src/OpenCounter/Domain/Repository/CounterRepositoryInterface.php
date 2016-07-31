<?php

namespace OpenCounter\Domain\Repository;


use OpenCounter\Domain\Model\Counter\Counter;
use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Domain\Model\Counter\CounterId;
/**
 * Repository for class Counter
 * This class is between Entity layer(class Counter) and access object layer(interface Storage).
 *
 * Repository encapsulates the set of objects persisted in a data store and the operations performed over them
 * providing a more object-oriented view of the persistence layer
 *
 * Repository also supports the objective of achieving a clean separation and one-way dependency
 * between the domain and data mapping layers
 *
 * Class CounterRepository
 */
interface CounterRepositoryInterface
{

  /**
   * Removes the counter given.
   *
   * @param OpenCounter\Domain\Model\Counter\Counter $anCounter The counter
   *
   * @return mixed
   */
  public function remove(Counter $anCounter);
  /**
   * Gets the counter of id given.
   *
   * @param OpenCounter\Domain\Model\Counter\CounterId $anId The counter id
   *
   * @return mixed
   */
  public function counterOfId(CounterId $anId);
  /**
   * Gets the counter of email given.
   *
   * @param OpenCounter\Domain\Model\Counter\CounterEmail $anEmail The counter email
   *
   * @return mixed
   */
//  public function counterOfEmail(CounterEmail $anEmail);
  /**
   * Gets the counter/counters that match with the given criteria.
   *
   * @param mixed $specification The specification criteria
   *
   * @return mixed
   */
  public function query($specification);
  /**
   * Returns the next available id.
   *
   * @return OpenCounter\Domain\Model\Counter\CounterId
   */
  public function nextIdentity();
  /**
   * Counts the number of counters.
   *
   * @return mixed
   */
  public function size();
}