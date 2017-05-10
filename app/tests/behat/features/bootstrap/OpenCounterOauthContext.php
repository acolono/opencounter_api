<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\WebApiExtension\Context\WebApiContext;

use Pavlakis\Slim\Behat\Context\App;
use Pavlakis\Slim\Behat\Context\KernelAwareContext;

use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Domain\Model\Counter\CounterName;
use OpenCounter\Domain\Model\Counter\Counter;

/**
 * Defines application features from the specific context.
 */
class OpenCounterOauthContext extends WebApiContext implements Context, SnippetAcceptingContext, KernelAwareContext
{
    use App;
    const GUZZLE_PARAMETERS = 'guzzle_parameters';
    protected $parameters;
    protected $headers = [];
    /**
     * @var GuzzleHttpClient
     */
    protected $client = null;
    /**
     * @var ResponseInterface
     */
    protected $response = null;
    /**
     * @var RequestInterface
     */
    protected $request = null;
    protected $requestBody = [];
    protected $data = null;
    /**
     * @var string
     */
    protected $refreshToken;
    protected $accessToken;
    protected $accessHeader;
    /**
     * @var string
     */
    protected $lastErrorJson;
    /**
     * @var bool
     */
    private $error;
    private $oauthContext;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
        $this->parameters = $parameters;
        $this->baseUrl = $parameters['base_url'];
    }

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
    }

    /**
     * @When I create oauth2 request
     */
    public function iCreateOauthRequest()
    {
        $this->iSetHeaderWithValue(
            'Content-Type',
            'application/x-www-form-urlencoded'
        );
        $this->iSetHeaderWithValue('Accept', 'application/json');
    }

    /**
     * @When I add the request parameters:
     */
    public function iAddTheRequestParameters(TableNode $parameters)
    {
        if ($parameters !== null) {
            foreach ($parameters->getRowsHash() as $key => $row) {
                $this->requestBody[$key] = $row;
            }
        }
    }

    /**
     * @When I send a access token request
     */
    public function iSendAAccessTokenRequest()
    {
        $url = $this->parameters['token_url'];
        $this->response = $this->getPostResponseFromUrl(
            $url,
            $this->requestBody
        );
        $this->printResponse();
    }

    /**
     * Get POST response from URL without ssl verify and exception propagation
     *
     * @param string $url POST URL
     * @param array $body $body array
     */
    protected function getPostResponseFromUrl($url, $body)
    {

        $this->request = $this->client->createRequest(
            'POST',
            $url,
            ['body' => $body, 'verify' => false, 'exceptions' => false]
        );

        return $this->response = $this->client->send($this->request);
    }

    /**
     * @Then the response status code is :code
     */
    public function theResponseStatusCodeIs($code)
    {
        $this->theResponseCodeShouldBe($code);
    }

    /**
     * @Then the response has a :propertyName property and it is equals :propertyValue
     */
    public function theResponseHasAPropertyAndItIsEquals(
        $propertyName,
        $propertyValue
    ) {
        $value = $this->theResponseHasAProperty($propertyName);

        if ($value == $propertyValue) {
            return;
        }
        throw new \Exception(sprintf(
            "Given %s value is not %s\n\n %s",
            $propertyName,
            $propertyValue,
            $this->echoLastResponse()
        ));
    }

    /**
     * @Then the response has a :propertyName property
     */
    public function theResponseHasAProperty($propertyName)
    {

        if ((isset($this->parameters['recommended'][$propertyName]) && !$this->parameters['recommended'][$propertyName])) {
            return;
        }
        if ((isset($this->parameters['optional'][$propertyName]) && !$this->parameters['optional'][$propertyName])) {
            return;
        }

        try {
            return $this->getPropertyValue($propertyName);
        } catch (\LogicException $e) {
            throw new \Exception(sprintf(
                "Property %s is not set!\n\n %s",
                $propertyName,
                $this->echoLastResponse()
            ));
        }
    }

    /**
     * Get property value from response data
     *
     * @param string $propertyName property name
     */
    protected function getPropertyValue($propertyName)
    {
        return $this->getValue($propertyName, $this->data);
    }

    /**
     * Get property value from data
     *
     * @param string $propertyName property name
     * @param mixed $data data as array or object
     */
    protected function getValue($propertyName, $data)
    {
        if (empty($data)) {
            throw new \Exception(sprintf(
                "Response was not set %s",
                var_export($data, true)
            ));
        }
        if (is_array($data) && array_key_exists($propertyName, $data)) {
            $data = $data[$propertyName];
            return $data;
        }
        if (is_object($data) && property_exists($data, $propertyName)) {
            $data = $data->$propertyName;
            return $data;
        }
        throw new \LogicException(sprintf("'%s' is not set", $propertyName));
    }

    /**
     * @When I add resource owner credentials
     */
    public function iAddResourceOwnerCredentials()
    {
        $this->requestBody['username'] = $this->parameters['oauth2']['username'];
        $this->requestBody['password'] = $this->parameters['oauth2']['password'];
    }

    /**
     * @Then the response is oauth2 format
     */
    public function theResponseIsOauthFormat()
    {
        $expectedHeaders = [
          'Cache-Control' => 'no-store, no-cache, must-revalidate, no-store',
          'Pragma' => 'no-cache, no-cache'
        ];

        foreach ($expectedHeaders as $name => $value) {
            $given = $this->response->getHeader($name);

            if ($given != $value) {
                throw new \Exception(sprintf(
                    "Header %s should be %s, %s given",
                    $name,
                    $value,
                    $given
                ));
            }
        }
    }

    /**
     * @Then the response has a :propertyName property and its type is :typeString
     */
    public function theResponseHasAPropertyAndItsTypeIsNumeric(
        $propertyName,
        $typeString
    ) {
        $value = $this->theResponseHasAProperty($propertyName);

        // check our type
        switch (strtolower($typeString)) {
            case 'numeric':
                if (is_numeric($value)) {
                    break;
                }
            case 'array':
                if (is_array($value)) {
                    break;
                }
            case 'null':
                if ($value === null) {
                    break;
                }
            default:
                throw new \Exception(sprintf(
                    "Property %s is not of the correct type: %s!\n\n %s",
                    $propertyName,
                    $typeString,
                    $this->echoLastResponse()
                ));
        }
    }

    /**
     * @Given that I have an refresh token
     */
    public function thatIHaveAnRefreshToken()
    {
        $parameters = $this->parameters['oauth2'];
        $parameters['grant_type'] = 'refresh_token';

        $url = $this->parameters['token_url'];
        $body_as_string = new PyStringNode($this->requestBody, 1);

        $this->response = $this->iSendARequestWithBody(
            'POST',
            $url,
            $body_as_string
        );

//      $response = $this->getPostResponseFromUrl($url, $parameters);
        $data = json_decode($this->response->getBody(true), true);

        if (!isset($data['refresh_token'])) {
            throw new \Exception(sprintf(
                "Error refresh token. Response: %s",
                $this->response->getBody(true)
            ));
        }
        $this->refreshToken = $data['refresh_token'];
    }

    /**
     * @When I make a access token request with given refresh token
     */
    public function iMakeAAccessTokenRequestWithGivenRefreshToken()
    {
        $this->requestBody['refresh_token'] = $this->refreshToken;
        $this->iMakeAAccessTokenRequest();
    }

    /**
     * @When I add client credentials
     */
    public function iAddClientCredentials()
    {
        $this->requestBody['client_id'] = $this->parameters['oauth2']['client_id'];
        $this->requestBody['client_secret'] = $this->parameters['oauth2']['client_secret'];
    }
}
