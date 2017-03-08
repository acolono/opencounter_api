<?php
/**
 * AddClientCommand.php
 *
 * contains a class that knows how to add oauth clients
 */
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
     *
     * @var string
     * @SWG\Property()
     */
    private $userId;
    /**
     * Client Id
     *
     * @SWG\Property()
     * @var string
     */
    private $client_id;
    /**
     * Client secret to use
     *
     * @SWG\Property()
     * @var
     */
    private $client_secret;
    /**
     * Scopes to add
     *
     * @SWG\Property(example="read:counters write:counters")
     * @var
     */
    private $scopes;
    /**
     * Grant types to allow
     * @SWG\Property(example="implicit client_credentials authorization_code")
     * @var
     */
    private $grant_types;
    /**
     * Url to redirect to
     *
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
     * @param $grant_types
     * @param $scopes
     * @param $user_id
     */
    public function __construct(
        $client_id,
        $client_secret,
        $redirect_url,
        $grant_types,
        $scopes,
        $user_id
    ) {
        $this->userId = $user_id;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_url = $redirect_url;
        $this->grant_types = $grant_types;
        $this->scopes = $scopes;
    }

    /**
     * User Id
     * @return string
     */
    public function userId()
    {
        return $this->userId;
    }

    /**
     * Client Id
     * @return string
     */
    public function clientId()
    {
        return $this->client_id;
    }

    /**
     * Client Secret
     * @return mixed
     */
    public function clientSecret()
    {
        return $this->client_secret;
    }

    /**
     * Scopes
     * @return mixed
     */
    public function scopes()
    {
        return $this->scopes;
    }

    /**
     * Grant types
     * @return mixed
     */
    public function grantTypes()
    {
        return $this->grant_types;
    }

    /**
     * url to redirect to
     * @return mixed
     */
    public function redirectUrl()
    {
        return $this->redirect_url;
    }
}
