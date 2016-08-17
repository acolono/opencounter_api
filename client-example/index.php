
<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 8/17/16
 * Time: 12:45 AM
 */

require __DIR__ . '/vendor/autoload.php';
include_once 'vendor/jl6m/swagger-lite/src/SwaggerClient.php';

use JL6m\SwaggerLite\SwaggerClient;
$client = new SwaggerClient([
  'scheme' => 'http',
  'swagger' => 'http://api.opencounter.docker/api/v1/docs',
  'auth' => ['client_id', 'client_secret'],
]);

$response = $client->get('counters/{name}/value', [
  'name' => 'onecounter',
]);
$results = $response->getBody();
echo $results;
