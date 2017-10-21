<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 8/17/16
 * Time: 12:45 AM
 */

require __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/../vendor/jl6m/swagger-lite/src/SwaggerClient.php';

use JL6m\SwaggerLite\SwaggerClient;


// Load Services which can move to dependency container later. these are external dependencies instanciated as services to be used by the app.

$loader = new Twig_Loader_Filesystem(__DIR__ . '/../themes/default_theme/templates');
$twig = new Twig_Environment($loader, array('debug' => true, 'cache' => false));
$loader->addPath(__DIR__ . '/../themes/swyfler_theme/templates');
$loader->addPath(__DIR__ . '/../themes/default_theme/source/_layouts');
$loader->addPath(__DIR__ . '/../themes/default_theme/source/_patterns',
  'patterns');
$loader->addPath(__DIR__ . '/../themes/default_theme/source/_macros');
$loader->addPath(__DIR__ . '/../themes/default_theme/source/_patterns/02-elements',
  'elements');
$loader->addPath(__DIR__ . '/../themes/default_theme/source/_patterns/05-layouts',
  'regions');
$loader->addPath(__DIR__ . '/../themes/default_theme/source/_patterns/00-atoms',
  'atoms');

// Hard settings not changable from inside the application used to instanciate the application with.
require __DIR__ . '/../config/settings.php';


$provider = new \ChrisHemmings\OAuth2\Client\Provider\Slimphp($config);

if (isset($_GET['code'])) {
    $message = 'Code: found';
//  if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
//
//    unset($_SESSION['oauth2state']);
//    $message = 'Access Token: invalid state';
//
//    exit('Invalid state');
//
//  }


    try {

        // Try to get an access token using the client credentials grant.
        $accessToken = $provider->getAccessToken('client_credentials');

    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

        // Failed to get the access token
        exit($e->getMessage());

    }
//  try {
//    $message = 'lets get a token';
//
//    // Try to get an access token using the authorization code grant.
//    $accessToken = $provider->getAccessToken('implicit', [
//      'code' => $_GET['code']
//    ]);
//
//    // We have an access token, which we may use in authenticated
//    // requests against the service provider's API.
//    echo 'Access Token: ' . $accessToken->getToken() . "<br>";
//    echo 'Refresh Token: ' . $accessToken->getRefreshToken() . "<br>";
//    echo 'Expired in: ' . $accessToken->getExpires() . "<br>";
//    echo 'Already expired? ' . ($accessToken->hasExpired() ? 'expired' : 'not expired') . "<br>";
//
//
//    $message = 'success';
//
//  } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
//
//    // Failed to get the access token or user details.
//    $message = 'fail';
//    exit($e->getMessage());
//
//  } catch (Exception $e) {
//    $message = $e->getMessage();
//
//    // Failed to get user details
//    exit($e->getMessage());
//  }
}

// if form was submitted with key save key as file
if (isset($_POST['submit'])):
    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: ' . $authUrl);
    exit;
endif;

/*
* Processors
*/

function CountHit($client, $config)
{
    $response = $client->patch('counters/value/{id}', [

      'id' => $config['counterId'],
    ]);
    return $response;
}

function GetTotalHits($client, $config)
{

    $response = $client->get('/counters/value/{id}', [
      'id' => $config['counterId'],
    ]);
    return $response;
}


// echo $results;


if (isset($accessToken)) {


    $client = new SwaggerClient([
      'scheme' => 'http',
      'swagger' => $config['apiHost'],
      'headers' =>
        [
          'Authorization' => "Bearer {$accessToken->getToken()}"
        ]
    ]);


    try {


        $allCountersResponse = GetTotalHits($client, $config);
        $allHitsResult = \GuzzleHttp\json_decode($allCountersResponse->getBody());


        $countHitResponse = CountHit($client, $config);
        $countHitResult = $countHitResponse->getBody();

    } catch (Exception $e) {

        $getTotalResponse = 'errored';
        $countHitResponse = 'errored';
        exit($e->getMessage());
    }

// load login page
    $template = $twig->load('pagehitcounter.html.twig');

    $results_to_show = 'not sure';

    if (isset($allHitsResult)) {
        $result_to_show = $allHitsResult;
    }

// display template with variable passed to it
    $prepared_render_array = [
      'is_configured' => (isset($config) ? true : false),
      'code_requested' => (isset($_GET['code']) ? true : ' no code'),
      'value' => $result_to_show,
      'counted' => (isset($countHitResult) ? $countHitResult : ' notcounted'),
    ];


    $template->display($prepared_render_array);


} else {

// load login page
    $template = $twig->load('reception.html.twig');

// display template with variable passed to it
    $prepared_render_array = array(
      'is_configured' => (isset($config) ? true : false),
      'code_requested' => (isset($_GET['code']) ? true : ' no code'),
      'results' => (isset($allHitsResult) ?: ' noresults'),
    );


    $template->display($prepared_render_array);


}