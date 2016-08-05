<?php
// Routes
$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("OpenCounter '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});


$app->get('/api/v1/docs', function($request, $response, $args) {
  $this->logger->info('gettin swagger');
  $swagger = \Swagger\scan(['../src']);
  header('Content-Type: application/json');
  return $response->withJson($swagger);
});

$app->get('/api/v1/counters/{name}', function($request, $response, $args) {
  $this->logger->info('getting counter with name: '. $args['name']);
  $counter_mapper = new \OpenCounter\Infrastructure\Persistence\Sql\SqlManager($this->db);
  $counterRepository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlPersistentCounterRepository($counter_mapper);
  $counterName = new \OpenCounter\Domain\Model\Counter\CounterName($args['name']);
  $counter = $counterRepository->getCounterByName($counterName);

  $this->logger->info(json_encode($counter));
  if($counter){
    $this->logger->info('found');
    return $response->withJson($counter, 200);
  } else {
    $this->logger->info('not found');
    //$response->write('resource not found');
    return $response->withStatus(404);
  }
});

$app->get('/api/v1/counters/{name}/value', function($request, $response, $args) {
  $this->logger->info('getting value from counter with id: 1' . $args['id']);
  $counter_mapper = new \OpenCounter\Infrastructure\Persistence\Sql\SqlManager($th);
  $counterRepository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlPersistentCounterRepository($counter_mapper);
  $counterName = new \OpenCounter\Domain\Model\Counter\CounterName($args['name']);
  $counter = $counterRepository->getCounterByName($counterName);
  $this->logger->info(json_encode($counter));

  if ($counter) {
    $this->logger->info('found');
    return $response->withJson($counter);
  } else {
    $this->logger->info('not found');
    //$response->write('resource not found');
    return $response->withStatus(404);
  }
});



$app->patch('/api/v1/counters/{name}', function ($request, $response, $args) {
  // Patch counter property  http://docs.slimframework.com/routing/patch/
  $this->logger->info('patching counter with name ' . $args['name']);

  $counter_mapper = new \OpenCounter\Infrastructure\Persistence\Sql\SqlManager($this->db);
  $counterRepository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlPersistentCounterRepository($counter_mapper);
  $counterName = new \OpenCounter\Domain\Model\Counter\CounterName($args['name']);

  $counter = $counterRepository->getCounterByName($counterName);
  if ($counter) {
    $this->logger->info('found');

    $data = $request->getParsedBody();

    $this->logger->info(json_encode($data));
    if(!isset($data)){
      $data = [ 'value' => 0 ];
    }
    if (isset($data['lock'])){
      $this->logger->info('locking counter with name '. $args['name']);
      $counter->lock();
    }
    if (isset($data['reset'])){
      $this->logger->info('resetting counter with name '. $args['name']);
      $counter->reset();
    }
    $counterRepository->save($counter);
    return $response->withJson($counter);

  } else {
    $this->logger->info('not found');
    //$response->write('resource not found');
    return $response->withStatus(404);
  }



});

$app->post('/api/v1/counters/{name}', function ($request, $response, $args) {
  $this->logger->info('inserting new counter with name ' . $args['name']);

  //  $counter_mapper = new OpenCounter\CounterMapper($this->db);
  $counter_mapper = new \OpenCounter\Infrastructure\Persistence\Sql\SqlManager($this->db);

  $data = $request->getParsedBody();

  $this->logger->info(json_encode($data));
  if(!isset($data)){
    $data = [ 'value' => 0, 'name' => 'OneCounter' ];
  }
  // Persisting a new counter
  // https://leanpub.com/ddd-in-php/read#leanpub-auto-persisting-value-objects

  $counterRepository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlPersistentCounterRepository($counter_mapper);
  $counterId = $counterRepository->nextIdentity();
  $counterName = new \OpenCounter\Domain\Model\Counter\CounterName($args['name']);
  $counterValue = new \OpenCounter\Domain\Model\Counter\CounterValue($data['value']);
  $counter = new \OpenCounter\Domain\Model\Counter\Counter($counterName, $counterId, $counterValue, 'passwordplaceholder');
  
  // dealing with duplicates
  if ($counterRepository->getCounterByName($counterName)) {
    return $response->withJson(
      ['message' => 'counter with name '. $counter->getName() . ' already exists'],
      409
    );
  }
  else {
    $counterRepository->save($counter);
    return $response->withJson($counter, 201);
  }
});

$app->put('/api/v1/counters/{name}/{password}', function ($request, $response, $args) {
  //we assume everything is going to fail
  $return = ['message' => 'an error has occurred'];
  $code = 400;
//  $counter_mapper = new OpenCounter\CounterMapper($this->db);
  $counter_mapper = new \OpenCounter\Infrastructure\Persistence\Sql\SqlManager($this->db);
  $counterRepository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlPersistentCounterRepository($counter_mapper);
  //var_dump($request);

  $this->logger->info(json_encode($data));
  $data = $request->getParsedBody();
  $counterName = new \OpenCounter\Domain\Model\Counter\CounterName($args['name']);
  $counterValue = new \OpenCounter\Domain\Model\Counter\CounterValue($data['value']);
  // validate the array
  if($data && isset($data['value'])){
    $counter = $counterRepository->getCounterByName($counterName);
    if($counter){
      if ($counter->isLocked()) {
        return $response->withJson(
            ['message' => 'counter with name '. $counterName->name() . ' is locked'],
            409
        );
      }
      else {

        $update = false;
        if($data['value'] === '+1'){
          $counter->value++;
          $update = true;
        } else if($data['value'] === '-1'){
          $counter->value--;
          $update = true;
        } else if(is_int($data['value'])){
          $counter->value = $data['value'];
          $update = true;
        } else {
          $return['message'] = 'Not a valid value, it should be either an integer or a "+1" or "-1" string';
        }

        if($update){
          $counterRepository->update($counter);
          $return = $response->withJson($counter);
          $code = 200;
        }
      }

    }else{
      $return['message'] = 'The counter was not found, possibly due to bad credentials';
      $code = 404;
    }
  }
  return $response->withJson($return, $code);

});
