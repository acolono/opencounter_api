
<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 8/17/16
 * Time: 12:45 AM
 */


require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
$client = new Client();
$response = $client->get('http://api.opencounter.docker/api/v1/counters/onecounter/value', [
  'headers' => ['Content-type' => 'application/json'],
]);
$results = $response->getBody();
echo $results;
