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
  header('Access-Control-Allow-Origin: *');
  return $response->withJson($swagger);
});




$app->get('/api/v1/counters/{id}', function($request, $response, $args) {
  $this->logger->info('getting counter with id: '. $args['id']);

//  $counter_mapper = new OpenCounter\CounterMapper($this->db);
  $counter_mapper = new \OpenCounter\Infrastructure\Persistence\Sql\SqlManager($this->db);
  $counterRepository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlPersistentCounterRepository($counter_mapper);
  $counterName = new \OpenCounter\Domain\Model\Counter\CounterName($args['id']);
  $counter = $counterRepository->getCounterByName($counterName);

  $this->logger->info(json_encode($counter));
  if($counter){
    $this->logger->info('found');
    return $response->withJson($counter);
  } else {
    $this->logger->info('not found');
    //$response->write('resource not found');
    return $response->withStatus(404);
  }
});

$app->get('/api/v1/counters/{id}/value', function($request, $response, $args) {
  $this->logger->info('getting value from counter with id: 1' . $args['id']);

  $counter_mapper = new \OpenCounter\Infrastructure\Persistence\Sql\SqlManager($this->db);
  $counterRepository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlPersistentCounterRepository($counter_mapper);
  $counterId = new \OpenCounter\Domain\Model\Counter\CounterId($args['id']);
  $counter = $counterRepository->getCounterByName($counterId);

  $this->logger->info(json_encode($counter));

  if($counter){
    $this->logger->info('found');
    return $response->withJson($counter);
  } else {
    $this->logger->info('not found');
    //$response->write('resource not found');
    return $response->withStatus(404);
  }
});



$app->patch('/api/v1/counters/{id}', function ($request, $response, $args) {
  // Patch counter property  http://docs.slimframework.com/routing/patch/
  $counter_mapper = new \OpenCounter\Infrastructure\Persistence\Sql\SqlManager($this->db);
  $counterRepository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlPersistentCounterRepository($counter_mapper);

  $counter = $counterRepository->getCounterById($args['id']);

  $data = $request->getParsedBody();

  $this->logger->info(json_encode($data));
  if(!isset($data)){
    $data = [ 'value' => 0 ];
  }

  $this->logger->info('patching counter with id '. $args['id']);
  $counter->reset();
});

$app->post('/api/v1/counters/{id}', function ($request, $response, $args) {
  $this->logger->info('inserting new counter with id '. $args['id']);

  //  $counter_mapper = new OpenCounter\CounterMapper($this->db);
  $counter_mapper = new \OpenCounter\Infrastructure\Persistence\Sql\SqlManager($this->db);


  $data = $request->getParsedBody();

  $this->logger->info(json_encode($data));
  if(!isset($data)){
    $data = [ 'value' => 0 ];
  }

  $counterRepository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlPersistentCounterRepository($counter_mapper);
  $counterId = new \OpenCounter\Domain\Model\Counter\CounterId($args['id']);
  $counterName = new \OpenCounter\Domain\Model\Counter\CounterName($data['name']);
  $counterValue = new \OpenCounter\Domain\Model\Counter\CounterValue($data['value']);
  $counter = new \OpenCounter\Domain\Model\Counter\Counter($counterName, $counterId, $counterValue, 'passwordplaceholder');

// TODO do something to persist now ,
  if($counterRepository->getCounterById($counterId)){
    return $response->withJson(
      ['message' => 'counter with id '.$counter->getId().' already exists'],
      409
    );
  }else{
    $counterRepository->save($counter);
    return $response->withJson($counter, 201);
  }
});

$app->put('/api/v1/counters/{id}/{password}', function ($request, $response, $args) {
  //we assume everything is going to fail
  $return = ['message' => 'an error has occurred'];
  $code = 400;
//  $counter_mapper = new OpenCounter\CounterMapper($this->db);
  $counter_mapper = new \OpenCounter\Infrastructure\Persistence\Sql\SqlManager($this->db);
  //var_dump($request);
  $data = $request->getParsedBody();
  $counterRepository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlPersistentCounterRepository($counter_mapper);
  // validate the array
  if($data && isset($data['value'])){
    $counter = $counterRepository->getCounterByCredentials($args['name'], $args['password']);
    if($counter){
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
        $counter_mapper->update($counter);
        return $response->withJson($counter);
      }
    }else{
      $return['message'] = 'The counter was not found, possibly due to bad credentials';
      $code = 404;
    }
  }
  return $response->withJson($return, $code);

});
