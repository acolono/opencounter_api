<?php

namespace OpenCounter\Http;

use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Model\Counter\Counter;
use OpenCounter\Domain\Model\Counter\CounterName;
use OpenCounter\Domain\Exception\Counter\CounterAlreadyExistsException;
use Monolog\Logger;

use OpenCounter\Domain\Repository\CounterRepositoryInterface;
use OpenCounter\Infrastructure\Factory\Counter\CounterFactory;

class CounterBuildService {


  private $counter_repository;

  private $counter_factory;
  private $logger;

  public function __construct(CounterRepositoryInterface $counter_repository, CounterFactory $counter_factory, Logger $logger)
  {

    $this->counter_repository = $counter_repository;
    $this->counter_factory = $counter_factory;
    $this->logger = $logger;
  }
  /**
   * {@inheritdoc}
   */
  public function execute($request = null, $args)
  {
//    if (!$request instanceof SignInCounterRequest) {
//      throw new \InvalidArgumentException('The request must be SignInCounterRequest instance');
//    }
    $data = $request->getParsedBody();
    $this->logger->info(json_encode($data));

    if(!isset($data)){
      $data = [ 'value' => 0, 'name' => 'OneCounter', 'status' => 'active' ];
    }

    // https://leanpub.com/ddd-in-php/read#leanpub-auto-persisting-value-objects

    $counterId = $this->counter_repository->nextIdentity();
    $name = new CounterName($data['name']);
    $value = new CounterValue($data['value']);

    $password = 'passwordplaceholder';
    $counter = $this->counter_repository->getCounterByName($name);
    $this->logger->info('testing during creation if counter exists ' . $name->name());

    if ($counter instanceof Counter) {
      throw new CounterAlreadyExistsException();
    }
    $counter = $this->counter_factory->build($counterId, $name, $value, 'active', $password);
    $this->logger->info('passing newly created counter to controller for saving via repo ' . $name->name());
    return $counter;
  }
}