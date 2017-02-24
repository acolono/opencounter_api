<?php
/**
 * OpenCounterServiceProvider.
 *
 * a way to inject counter services into the container so they can be used in the app.
 */
namespace SlimCounter\Infrastructure\ServiceProvider;

use Chadicus\Slim\OAuth2\Middleware;
use OAuth2\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\Views;
use SlimCounter\Application\Command\Oauth2\AddClientHandler;
use SlimCounter\Application\Service\Oauth2\AddClientService;

class Oauth2ServiceProvider implements ServiceProviderInterface
{
    /**
     * The provides array is a way to let the container
     * know that a service is provided by this service
     * provider. Every service that is registered via
     * this service provider must have an alias added
     * to this array or it will be ignored.
     *
     * @var array
     */
    protected $provides = [
      'oauth2_storage',
      'add_client_application_service',
      'authorization',
      'authorization_views',

    ];

    /**
     * This is where the magic happens, within the method you can
     * access the container and register or retrieve anything
     * that you need to, but remember, every alias registered
     * within this method must be declared in the `$provides` array.
     */
    public function register(Container $pimple)
    {

        $pimple['authorization'] = $pimple->factory(function ($pimple) {

// setup authorization middleware to protect routes
            $authorization = new Middleware\Authorization($pimple['oauth2_server'],
              $pimple);

            return $authorization;
        });
//

        $pimple['authorization_views'] = function ($pimple) {

//// TODO: where best to log when a user uses a token to access counter routes
//$token = $container->get('oauth2_server')->getAccessTokenData(OAuth2\Request::createFromGlobals());
//$user_id = $token['user_id'];

// Auth Routes and renderer of templates for oauth confirmations
            $authorization_views = new Views\PhpRenderer(APP_ROOT . '/vendor/chadicus/slim-oauth2-routes/templates');

            return $authorization_views;
        };
        /**
         * Application service for signing up new users
         *
         * user signup triggers email verification
         * or uses valid  verification or invite token
         *
         * @param $container
         * @return \BenGorUser\CarlosBuenosvinosDddBridge\Application\Service\SignUp\SignUpUserService
         */
        $pimple['add_client_application_service'] = $pimple->factory(function (
          $pimple
        ) {
            // first try without command bus dependency
            $add_client_application_service = new AddClientService(
              new AddClientHandler(
                $pimple['oauth2_storage']
              )
            );

            return $add_client_application_service;
        });

        /**
         * Oauth Storage
         *
         * can be used to interact with oauth2 data in database
         * but really just is needed to instanciate the oauth server
         *
         * @param $container
         * @return \OAuth2\Storage\Pdo
         */
        $pimple['oauth2_storage'] = function ($pimple) {

            $oauth2_storage = new \OAuth2\Storage\Pdo($pimple['pdo']);

            return $oauth2_storage;
        };

        $pimple['oauth2_server'] = function ($pimple) {

//Setup Auth
            $oauth2_server = new Server(
              $pimple['oauth2_storage'],
              [
                  //    'access_lifetime' => 3600,
                'allow_implicit' => true,
              ],
              [

//                    new \OAuth2\GrantType\UserCredentials($pimple['oauth2_storage']),
                new \OAuth2\GrantType\ClientCredentials($pimple['oauth2_storage']),
//                new \OAuth2\GrantType\AuthorizationCode($pimple['oauth2_storage']),
//                    new \OAuth2\GrantType\RefreshToken($pimple['oauth2_storage']),
              ]
            );

            return $oauth2_server;
        };
    }
}
