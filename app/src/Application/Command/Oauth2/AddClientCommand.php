<?php

namespace SlimCounter\Application\Command\Oauth2;

class AddClientCommand
{
    private $user_id;
    private $client_id;
    private $client_secret;
    private $scopes;
    private $grant_types;
    private $redirect_url;

    /**
     * @param string $user_id
     */
    public function __construct($client_id, $client_secret, $redirect_url, $scopes, $grant_types, $user_id)
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
