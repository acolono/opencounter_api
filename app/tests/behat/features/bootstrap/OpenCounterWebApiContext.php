<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\WebApiExtension\Context\WebApiContext;
use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Model\Counter\CounterName;
use Pavlakis\Slim\Behat\Context\App;
use Pavlakis\Slim\Behat\Context\KernelAwareContext;

/**
 * Defines application features from the specific context.
 */
class OpenCounterWebApiContext extends WebApiContext implements Context, SnippetAcceptingContext, KernelAwareContext
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
private $counters;
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
        // TODO: should we be getting them from the container here since we are kernel aware? probably
//        $this->counter_factory = new \OpenCounter\Infrastructure\Factory\Counter\CounterFactory();

        // TODO: why can we access app in our steps but not here in construct?
//        $this->db = $this->app->getContainer()->get('pdo');
//        $this->sqlManager = $this->app->getContainer()->get('counter_mapper');
        $this->counters = [];
//        $this->oauth_storage = $this->app->getContainer()->get('oauth_storage');
    }

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->oauthContext = $environment->getContext('RstGroup\Behat\OAuth2\Context\OAuth2Context');
        // lets insert a valid access token before each scenario and remove after
        // set them from context parameters shouldnt be neccessary
        $access_token = 'abxc';
        $client_id = 'abxc';
        $user_id = 'abxc';
        $time = 'tomorrow';
        $expires = strtotime($time);
        $scope = 'write:counters read:counters';

        $this->app->getContainer()
          ->get('oauth2_storage')
          ->setAccessToken($access_token, $client_id, $user_id, $expires,
            $scope);
        $this->accessToken = $access_token;
    }

    /**
     * @AfterScenario
     */
    public function cleanDB(\Behat\Behat\Hook\Scope\AfterScenarioScope $scope)
    {
        $this->db = $this->app->getContainer()->get('pdo');
        $this->sqlManager = $this->app->getContainer()->get('counter_mapper');
        $this->counterRepository = $this->app->getContainer()
          ->get('counter_repository');

        $this->app->getContainer()
          ->get('oauth2_storage')
          ->unsetAccessToken($this->accessToken);
        if (isset($this->counters) && is_array($this->counters)) {
            echo 'removing testing counters';

            // foreach $created_counters as counter delete counter
            foreach ($this->counters as $counter) {
                $this->counterRepository->remove($counter);
            }
        }
    }

    /**
     * @Given a counter :name with ID :id and a value of :value has been set
     */
    public function aCounterWithIdAndAValueOfWasAddedToTheCollection(
      $name,
      $id,
      $value
    ) {
        $endpoint = '/api/counters/' . $id;
        // send a POST request to the endpoint with the counter values in the body
        $newCounterArray = [
          json_encode(['anId' => $id, 'name' => $name, 'value' => $value])
        ];
        $newCounterjsonString = new PyStringNode($newCounterArray, 1);
        $this->iSetHeaderWithValue('Content-Type', 'application/json');
        $this->iSetHeaderWithValue('Accept', 'application/json');
//        $this->oauthContext->iHaveValidAccessToken();
        $this->iSetHeaderWithValue('Authorization', $this->accessToken);

//        TODO: try authenticatink with valid access token as api key header.
        $this->iSetHeaderWithValue('api_key', 'testtoken');
//        TODO: make sure we send valid authentication in all requests we make in this context. we are not testing authorization layer
        $this->iSendARequestWithBody('POST', $endpoint, $newCounterjsonString);
        $this->printResponse();

        // get counter object from db and remember it so we can delete it later.

        $this->db = $this->app->getContainer()->get('pdo');
        $this->sqlManager = $this->app->getContainer()->get('counter_mapper');
        $this->counterRepository = $this->app->getContainer()
          ->get('counter_repository');

        $counter = $this->counterRepository->getCounterByName($name);
        $this->counters[] = $counter;
        // since we are using this as a given step we can make sure it was added successfully within this step
        $this->theResponseShouldContain('id');
        $this->theResponseCodeShouldBe('201');

        //make absolutely sure we added it successfully and our cleanup works
        $errormesage = ['message' => "counter with id $id already exists"];
        $ErrorString = new PyStringNode($newCounterArray, 1);
        $this->theResponseShouldNotContain($ErrorString);
    }

    /**
     * @Then the value returned should be :arg1
     */
    public function theValueReturnedShouldBe($arg1)
    {
        $this->theResponseShouldContain($arg1);
    }

    /**
     * @When I increment the value of the counter with ID :id
     */
    public function iIncrementTheValueOfTheCounterWithId($id)
    {
        $endpoint = '/api/counters/value/'  . $id ;

        $CounterArray = [
          json_encode(['value' => '+1'])
        ];
//      [$rowLineNumber => [$val1, $val2, $val3]]
        $CounterStringNode = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('PUT', $endpoint, $CounterStringNode);
        $this->printResponse();

    }

    /**
     * @When I increment the value of the counter with Name :name
     */
    public function iIncrementTheValueOfTheCounterWithName($name)
    {
        $endpoint = '/api/counters/value';

        $CounterArray = [
          json_encode([
            'name' => $name,
            'value' => 1
          ])
        ];
        // [$rowLineNumber => [$val1, $val2, $val3]]
        $CounterStringNode = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('PATCH', $endpoint, $CounterStringNode);
        $this->printResponse();

    }

    /**
     * @When I lock the counter with ID :id
     */
    public function iLockTheCounterWithId($id)
    {
        $endpoint = '/api/counters/status';

        $CounterArray = [
          json_encode(['id' => $id, 'value' => '1', 'status' => 'locked'])
        ];
//      [$rowLineNumber => [$val1, $val2, $val3]]
        $CounterStringNode = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('PATCH', $endpoint, $CounterStringNode);
        $this->printResponse();
    }

    /**
     * @When I lock the counter with Name :name
     */
    public function iLockTheCounterWithName($name)
    {
        // TODO: do we need to get the id to do this or can we patch to the generic endpoint
        $endpoint = '/api/counters/status';

        $CounterArray = [
          json_encode([
            'name' => $name,
            'status' => 'locked',
            'value' => 0
          ])
        ];
//      [$rowLineNumber => [$val1, $val2, $val3]]
        $CounterStringNode = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('PATCH', $endpoint, $CounterStringNode);
        $this->printResponse();

    }

    /**
     * @Then I should see an error :message
     */
    public function iShouldSeeAnError($message)
    {
        $errormesage = ['message' => $message];
        $ErrorString = new PyStringNode($errormesage, 1);
        $this->theResponseShouldContain($ErrorString);
    }

    /**
     * @When I get the value of the counter with ID :id
     */
    public function iGetTheValueOfTheCounterWithId($id)
    {
        $this->iSendARequest('GET', "api/counters/$id/value");
    }

    /**
     * @When I (can )get the value of the counter with Name :name
     */
    public function iGetTheValueOfTheCounterWithName($name)
    {
        // we expect our client to know the id and use that,
        // we need to make sure the client can get at the id
        // somehow from the name somewhere else.
        // during this test we just get the id from the repository directly. (or using our service)
        // getting the counter id from its name deserves its own scenario.
        // TODO: get counter id from name, consider whether we expect a hateoas link or just the id back when we look for a name (in seperate scenario)






        $this->iGetTheValueOfTheCounterWithId($id);
    }


    /**
     * @When I get the Id of the counter with Name :arg1
     */
    public function iGetTheIdOfTheCounterWithName($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then the Id returned should be :arg1
     */
    public function theIdReturnedShouldBe($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When I reset the counter with ID :id
     */
    public function iResetTheCounterWithId($id)
    {
        $endpoint = '/api/counters/' . $id;

        $CounterArray = [
          json_encode(['value' => 0])
        ];
//      [$rowLineNumber => [$val1, $val2, $val3]]
        $CounterStringNode = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('PATCH', $endpoint, $CounterStringNode);
    }

    /**
     * @When I reset the counter with Name :name
     */
    public function iResetTheCounterWithName($name)
    {
        $endpoint = '/api/counters/' . $name . '/passwordplaceholder';

        $CounterArray = [
          json_encode(['value' => 0])
        ];
        // [$rowLineNumber => [$val1, $val2, $val3]]
        $CounterStringNode = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('PUT', $endpoint, $CounterStringNode);
        $this->printResponse();
    }

    /**
     * @Then no counter with id :arg1 has been set
     */
    public function noCounterWithIdHasBeenSet($id)
    {
        // get the counter we added to db and remember it so we can delete it later
        $this->db = $this->app->getContainer()->get('pdo');
        $this->sqlManager = $this->app->getContainer()->get('counter_mapper');
        $this->counterRepository = $this->app->getContainer()
          ->get('counter_repository');
        $counter = $this->counterRepository->getCounterById(new CounterId($id));

        // if we get a counter something is wrong
        if ($counter) {
            throw new \Exception('something is wrong, seems a counter is in the database');
        }
    }

    /**
     * @Given no counter :name has been set
     */
    public function noCounterHasBeenSet($name)
    {

        // get the counter we added to db and remember it so we can delete it later
        $this->db = $this->app->getContainer()->get('pdo');
        $this->sqlManager = $this->app->getContainer()->get('counter_mapper');
        $this->counterRepository = $this->app->getContainer()
          ->get('counter_repository');
        $counter = $this->counterRepository->getCounterByName(new CounterName($name));

        // if we get a counter something is wrong
        if ($counter) {
            throw new \Exception('something is wrong, seems a counter is in the database');
        }

    }
    /**
     * This doenst clean up after itsself so we can test counter removal.
     *
     * TODO: has been set doesnt need to use the webapi. if we want to use the webapi we use: i set a. consider using step from lower context
     * @Given a counter with id :id has been set
     *
     */
    public function aCounterWithIdHasBeenSet($id)
    {

        // lets use the factory to create the counter here, but not bother with using the build Service
        // TODO we are not testing here just setting up a convinience function, could use this directly from domaincontext actually
        $CounterFactory = new \OpenCounter\Infrastructure\Factory\Counter\CounterFactory();
        $this->counter = $CounterFactory->build(
          new CounterId($id),
          new CounterName('testcounter'),
          new \OpenCounter\Domain\Model\Counter\CounterValue(0),
          'active',
          'passwordplaceholder');

        $this->counter_repository = $this->app->getContainer()->get('counter_repository');

        $this->counter_repository->save($this->counter);


    }

    /**
     * NOTE : we do have a httpbuildcounterservice but are using
     *  specific command / query services by now.
     * consider using and testing the httpbuildservice at a later point.
     * but for now just ensure that after this task there is a counter with the correct id.
     *
     * @Given a counter with id :id has been set
     */
//    public function aCounterWithIdHasBeenSet($id)
//    {
////        // testing the counterbuildservice requires us to create a request object
////        $name = 'democounter';
////        $uri = \Slim\Http\Uri::createFromString($this->baseUrl);
////        $headers = new \Slim\Http\Headers();
////        $cookies = [];
////        $serverParams = [];
////        $body = new \Slim\Http\Body(fopen('php://temp', 'r+'));
////        $args = ['name' => $name, 'id' => $id, 'value' => 0];
////
////        $request = new \Slim\Http\Request('GET', $uri, $headers, $cookies,
////          $serverParams, $body);
////
////        $request = $request->withParsedBody($args);
////
////        // now test the build service just in case
////        // cant test build service without request
////        /
////
////        $this->counters[] = $this->app->getContainer()
////          ->get('counter_build_service')
////          ->execute($request, $args);
//
//    }

    /**
     * @When I remove the counter with id :id
     */
    public function iRemoveTheCounterWithId($id)
    {
        $endpoint = '/api/counters/' . $id;

        $CounterArray = [
          json_encode(['value' => 0, 'id' => $id])
        ];
        // [$rowLineNumber => [$val1, $val2, $val3]]
        $CounterStringNode = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('DELETE', $endpoint, $CounterStringNode);
        $this->printResponse();
        $this->theResponseCodeShouldBe('200');
    }

    /**
     * @When I remove the counter with name :name
     */
    public function iRemoveTheCounterWithName($name)
    {
        $endpoint = '/api/counters/' . $name . '/passwordplaceholder';

        $CounterArray = [
          json_encode(['value' => 0, 'name' => $name])
        ];
        // [$rowLineNumber => [$val1, $val2, $val3]]
        $CounterStringNode = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('DELETE', $endpoint, $CounterStringNode);
        $this->printResponse();
    }

    /**
     * @Given a counter with name :name has been set
     */
    public function aCounterWithNameHasBeenSet($name)
    {
        $this->aCounterHasBeenSet($name);
    }

    /**
     * @Given a counter :name has been set
     */
    public function aCounterHasBeenSet($name)
    {
        $this->aCounterWithValueOfWasAddedToTheCollection($name, 0);

    }

    /**
     * @Given a counter :name with a value of :value has been set
     */
    public function aCounterWithValueOfWasAddedToTheCollection($name, $value)
    {

        $endpoint = '/api/counters/';
        // send a POST request to the endpoint with the counter values in the body
        $newCounterArray = [
          json_encode([
            'name' => $name,
            'value' => $value
          ])
        ];
        $newCounterjsonString = new PyStringNode($newCounterArray, 1);
        $this->iSetHeaderWithValue('Content-Type', 'application/json');

        $this->accessHeader = 'Bearer ' . $this->accessToken;
        $this->iSetHeaderWithValue('Authorization', $this->accessHeader);

        $this->iSetHeaderWithValue('Accept', 'application/json');
        $this->iSendARequestWithBody('POST', $endpoint, $newCounterjsonString);
        $response = $this->printResponse();

        // get the counter we added to db and remember it so we can delete it later
        $this->db = $this->app->getContainer()->get('pdo');
        $this->sqlManager = $this->app->getContainer()->get('counter_mapper');
        $this->counterRepository = $this->app->getContainer()
          ->get('counter_repository');
        $this->counterName = new CounterName($name);
        $counter = $this->counterRepository->getCounterByName($this->counterName);

        $this->counters[] = $counter;

        // since we are using this as a given step we can make sure it was added successfully within this step
        //$this->theResponseShouldContain('id');

        $this->theResponseCodeShouldBe('201');

        //make absolutely sure we added it successfully and our cleanup works
        $errormesage = ['message' => "counter with name $name already exists"];
        $ErrorString = new PyStringNode($newCounterArray, 1);
        $this->theResponseShouldNotContain($ErrorString);
    }

}
