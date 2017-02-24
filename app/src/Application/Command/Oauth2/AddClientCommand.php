<?php

namespace SlimCounter\Application\Command\Oauth2;

/**
 * Class AddClientCommand
 *
 * @SWG\Definition()
 *
 * @package SlimCounter\Application\Command\Oauth2
 */
class AddClientCommand
{
    /**
     * User Id
     * @var string
     * @SWG\Property()
     */
    private $user_id;
    /**
     * Client Id
     * @SWG\Property()
     * @var string
     */
    private $client_id;
    /**
     * @SWG\Property()
     * @var
     */
    private $client_secret;
    /**
     * @SWG\Property(example="read:counters write:counters")
     * @var
     */
    private $scopes;
    /**
     * @SWG\Property(example="implicit client_credentials authorization_code")
     * @var
     */
    private $grant_types;
    /**
     * @SWG\Property()
     * @var
     */
    private $redirect_url;

    /**
     * AddClientCommand constructor.
     *
     * @param $client_id
     * @param $client_secret
     * @param $redirect_url
     * @param $scopes
     * @param $grant_types
     * @param $user_id
     */
    public function __construct(
      $client_id,
      $client_secret,
      $redirect_url,
      $grant_types,
      $scopes,
      $user_id)
    {
        $this->user_id = $user_id;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_url = $redirect_url;
        $this->scopes = $scopes;
        $this->grant_types = $grant_types;
    }

    /**
     * @return string
     */
    public function user_id()
    {
        return $this->user_id;
    }

    /**
     * @return string
     */
    public function client_id()
    {
        return $this->client_id;
    }

    /**
     * @return string
     */
    public function client_secret()
    {
        return $this->client_secret;
    }

    /**
     * @return string
     */
    public function scopes()
    {
        return $this->scopes;
    }

    /**
     * @return string

     */
    public function grant_types()
    {
        return $this->grant_types;
    }

    public function redirect_url()
    {
        return $this->redirect_url;
    }
}
