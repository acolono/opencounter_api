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
class OpenCounterWebApiContext extends WebApiContext implements Context, SnippetAcceptingContext, KernelAwareContext
{
    use App;
  protected $parameters;
    /**
     * @var bool
     */
    private $error;
  const GUZZLE_PARAMETERS = 'guzzle_parameters';

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
        // TODO: should we be getting them from the container here since we are kernel aware? probably
//        $this->logger = $this->app->getContainer()->get('logger');
//        $this->counter_factory = new \OpenCounter\Infrastructure\Factory\Counter\CounterFactory();
//        $this->db = $this->app->getContainer()->get('db');
//        $this->sqlManager = $this->app->getContainer()->get('counter_mapper');
//       // $this->counterRepository = $this->app->getContainer()->get('counter_repository');
        $this->counters = array();

//        $this->counterBuildService = $this->app->getContainer()->get('counterBuildService');
    }

  /** @BeforeScenario */
  public function gatherContexts(BeforeScenarioScope $scope)
  {
    $environment = $scope->getEnvironment();

    $this->oauthContext = $environment->getContext('RstGroup\Behat\OAuth2\Context\OAuth2Context');
  }
    /**
     * @AfterScenario
     */
    public function cleanDB(\Behat\Behat\Hook\Scope\AfterScenarioScope $scope)
    {

        $this->db = $this->app->getContainer()->get('db');
        $this->sqlManager = $this->app->getContainer()
            ->get('counter_mapper');
        $this->counterRepository = $this->app->getContainer()
          ->get('counter_repository');
//    $this->counterRepository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlPersistentCounterRepository($this->sqlManager);

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
        $newCounterArray = array(
          json_encode(array('anId' => $id, 'name' => $name, 'value' => $value))
        );
        $newCounterjsonString = new PyStringNode($newCounterArray, 1);
        $this->iSetHeaderWithValue('Content-Type', 'application/json');
        $this->iSetHeaderWithValue('Accept', 'application/json');
        $this->oauthContext->iHaveValidAccessToken();
        $this->iSetHeaderWithValue('Authorization', $this->accessToken);

//        TODO: try authenticatink with valid access token as api key header.
      $this->iSetHeaderWithValue('api_key', 'testtoken');
//        TODO: make sure we send valid authentication in all requests we make in this context. we are not testing authorization layer
        $this->iSendARequestWithBody('POST', $endpoint, $newCounterjsonString);
        $this->printResponse();

        // get counter object from db and remember it so we can delete it later.

        $this->db = $this->app->getContainer()->get('db');
        $this->sqlManager = $this->app->getContainer()
            ->get('counter_mapper');
        $this->counterRepository = $this->app->getContainer()
            ->get('counter_repository');

        $counter = $this->counterRepository->getCounterByName($name);
        $this->counters[] = $counter;
        // since we are using this as a given step we can make sure it was added successfully within this step
        $this->theResponseShouldContain('id');
        $this->theResponseCodeShouldBe('201');

        //make absolutely sure we added it successfully and our cleanup works
        $errormesage = array('message' => "counter with id $id already exists");
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
        $endpoint = '/api/counters/' . $id . '/passwordplaceholder';

        $CounterArray = array(
          json_encode(array('value' => '+1'))
        );
//      [$rowLineNumber => [$val1, $val2, $val3]]
        $CounterjsonString = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('PUT', $endpoint, $CounterjsonString);
        $this->printResponse();

    }

    /**
     * @When I increment the value of the counter with Name :name
     */
    public function iIncrementTheValueOfTheCounterWithName($name)
    {
        $endpoint = '/api/counters/' . $name . '/value';

        $CounterArray = [
          json_encode(['value' => 1])
        ];
        // [$rowLineNumber => [$val1, $val2, $val3]]
        $CounterjsonString = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('PATCH', $endpoint, $CounterjsonString);
        $this->printResponse();

    }

    /**
     * @When I lock the counter with ID :id
     */
    public function iLockTheCounterWithId($id)
    {
        $endpoint = '/api/counters/' . $id . '/status';

        $CounterArray = [
          json_encode(['value' => '1', 'status' => 'locked'])
        ];
//      [$rowLineNumber => [$val1, $val2, $val3]]
        $CounterjsonString = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('PATCH', $endpoint, $CounterjsonString);
        $this->printResponse();
    }

    /**
     * @When I lock the counter with Name :name
     */
    public function iLockTheCounterWithName($name)
    {
        $endpoint = '/api/counters/' . $name . '/status';

        $CounterArray = [
          json_encode([
            'status' => 'locked',
            'value' => 0
          ])
        ];
//      [$rowLineNumber => [$val1, $val2, $val3]]
        $CounterjsonString = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('PATCH', $endpoint, $CounterjsonString);
        $this->printResponse();

    }

    /**
     * @Then I should see an error :message
     */
    public function iShouldSeeAnError($message)
    {
        $errormesage = array('message' => $message);
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
        $this->iSendARequest('GET', "api/counters/$name/value");
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
        $CounterjsonString = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('PATCH', $endpoint, $CounterjsonString);
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
        $CounterjsonString = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('PUT', $endpoint, $CounterjsonString);
        $this->printResponse();
    }

    /**
     * @Then no counter with id :arg1 has been set
     */
    public function noCounterWithIdHasBeenSet($id)
    {
        // get the counter we added to db and remember it so we can delete it later
        $this->db = $this->app->getContainer()->get('db');
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
        $this->db = $this->app->getContainer()->get('db');
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
     * @When I set a counter with name :name
     */
    public function iSetACounterWithName($name)
    {
      $this->aCounterWithValueOfWasAddedToTheCollection($name, 0);
    }

    /**
     * @Given a counter :name with a value of :value has been set
     */
  public function aCounterWithValueOfWasAddedToTheCollection($name, $value)
    {
      $endpoint = '/api/counters/' . $name;
      // send a POST request to the endpoint with the counter values in the body
      $newCounterArray = array(
        json_encode(array(
          'name' => $name,
          'uuid' => 'demouuid',
          'value' => $value
        ))
      );
//      [$rowLineNumber => [$val1, $val2, $val3]]
    $newCounterjsonString = new PyStringNode($newCounterArray, 1);
    $this->iSetHeaderWithValue('Content-Type', 'application/json');
    // TODO: authenticate using valid access token or bypass auth layer entirely so we just test counter routes: right now counter routes need an oauth access token but that should not really matter here since authentication and authorization get their own tests. here we just make sure the webapi for counters works. so where can i best ensure these tests ignore the oauth access layer maybe use parts of oauth context here to authenticate? http://docs.behat.org/en/v3.0/cookbooks/accessing_contexts_from_each_other.html
    $this->oauthContext->iHaveValidAccessToken();
    $this->accessHeader = 'Bearer ' . $this->oauthContext->accessToken;
    $this->iSetHeaderWithValue('Authorization', $this->accessHeader);

    $this->iSetHeaderWithValue('Accept', 'application/json');
    $this->iSendARequestWithBody('POST', $endpoint, $newCounterjsonString);
    $response = $this->printResponse();

    // get the counter we added to db and remember it so we can delete it later
    $this->db = $this->app->getContainer()->get('db');
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
    $errormesage = array('message' => "counter with name $name already exists");
    $ErrorString = new PyStringNode($newCounterArray, 1);
    $this->theResponseShouldNotContain($ErrorString);
    }


    /**
     * @Given a counter with id :id has been set
     */
    public function aCounterWithIdHasBeenSet($id)
    {
        // testing the counterbuildservice requires us to create a request object
        $name = 'democounter';
        $uri = \Slim\Http\Uri::createFromString($this->baseUrl);
        $headers = new \Slim\Http\Headers();
        $cookies = [];
        $serverParams = [];
        $body = new \Slim\Http\Body(fopen('php://temp', 'r+'));
        $args = ['name' => $name, 'id' => $id, 'value' => 0];

        $request = new \Slim\Http\Request('GET', $uri, $headers, $cookies,
            $serverParams, $body);

        $request = $request->withParsedBody($args);

        // now test the build service just in case
        // cant test build service without request
        $this->counters[] = $this->app->getContainer()->get('counter_build_service')->execute($request, $args);


    }

    /**
     * @When I remove the counter with id :id
     */
    public function iRemoveTheCounterWithId($id)
    {
        $endpoint = '/api/counters/' . $id . '/passwordplaceholder';

        $CounterArray = [
          json_encode(['value' => 0, 'id' => $id])
        ];
        // [$rowLineNumber => [$val1, $val2, $val3]]
        $CounterjsonString = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('DELETE', $endpoint, $CounterjsonString);
        $this->printResponse();
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
        $CounterjsonString = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('DELETE', $endpoint, $CounterjsonString);
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
  public function aCounterHasBeenSet($name) {
    $this->aCounterWithValueOfWasAddedToTheCollection($name, 0);

  }

}
