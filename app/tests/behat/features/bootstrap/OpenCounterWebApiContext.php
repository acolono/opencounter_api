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

    private $counterRepository;

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

        $this->counters = [];
    }

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

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
          ->setAccessToken(
              $access_token,
              $client_id,
              $user_id,
              $expires,
              $scope
          );
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
     * Utility since we dont set counters with ids via service layer
     *
     * @Given a counter with id :id has been set
     */

    public function aCounterWithIdhasBeenSet($id)
    {
        $name = 'testname';
        $value = "1";
        $this->aCounterWithIdAndAValueOfWasAddedToTheCollection(
            $name,
            $id,
            $value
        );
    }

    /**
     * adding counters with a specific id
     * isnt possible via the application layer.
     * instead this is just a utility to
     * create a counter with id for later test steps
     * and uses the domain context for it
     * @Given a counter :name with ID :id and a value of :value was added to the collection
     */
    public function aCounterWithIdAndAValueOfWasAddedToTheCollection(
        $name,
        $id,
        $value
    ) {
        $this->counterName = new CounterName($name);
        $this->counterId = new CounterId($id);
        $this->counterValue = new \OpenCounter\Domain\Model\Counter\CounterValue(0);
        $this->counter_repository = $this->app->getContainer()
          ->get('counter_repository');
        $this->counter_factory = new \OpenCounter\Infrastructure\Factory\Counter\CounterFactory();

        // lets use the factory to create the counter here, but not bother with using the build Service
        // TODO we are not testing here just setting up a convinience function,
        // could use this directly from domaincontext actually but there we arent saving the counter
        // $this->domainContext->aCounterWithIdhasBeenSet($id);
        // gotta make sure we got the factory or just create one
        $this->counter = $this->counter_factory->build(
            $this->counterId,
            $this->counterName,
            $this->counterValue,
            'active',
            'passwordplaceholder'
        );

        $this->counter_repository->save($this->counter);

        // mark for removal
        $this->counters[] = $this->counter;
    }

    /**
     * @Then the value returned should be :arg1
     */
    public function theValueReturnedShouldBe($arg1)
    {
        $this->printResponse();
        $this->theResponseShouldContain($arg1);
    }

    /**
     * @When I increment the value of the counter with Name :name
     */
    public function iIncrementTheValueOfTheCounterWithName($name)
    {

        // just a helper until we implemented an endpoint
        $id = $this->iGetTheIdOfTheCounterWithName($name);
        $this->iIncrementTheValueOfTheCounterWithId($id);
    }

    /**
     * TODO: we can not yet get id through webapi
     * and are using application service directly
     * to get the other tests to complete.
     * we will need to expose an endpoint where
     * clients can query for counter id by counter
     * name though and replace this step definition.
     * @When I (can )get the Id of the counter with Name :name
     */
    public function iGetTheIdOfTheCounterWithName($name)
    {
        $this->CounterViewUiService = $this->app->getContainer()
          ->get('CounterViewUiService');

        try {
            $counter = $this->CounterViewUiService->execute(
                new \OpenCounter\Application\Query\Counter\CounterOfNameQuery($name)
            );
        } catch (Exception $e) {
            $error = ['message' => $e->getMessage()];

            return $error;
        }

        if (isset($counter)) {
            return $counter->getId();
        }
    }

    /**
     * @When I increment the value of the counter with ID :id
     */
    public function iIncrementTheValueOfTheCounterWithId($id)
    {
        $endpoint = '/api/counters/value/' . $id;

        $CounterArray = [
          json_encode(['value' => '+1'])
        ];
        $CounterStringNode = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('PATCH', $endpoint, $CounterStringNode);
        $this->printResponse();
    }

    /**
     * @When I lock the counter with Name :name
     */
    public function iLockTheCounterWithName($name)
    {

        // just a helper until we implemented an endpoint
        $id = $this->iGetTheIdOfTheCounterWithName($name);
        // TODO: do we need to get the id to do this or can we patch to the generic endpoint
        $this->iLockTheCounterWithId($id);
    }

    /**
     * @When I lock the counter with ID :id
     */
    public function iLockTheCounterWithId($id)
    {
        $endpoint = '/api/counters/status/' . $id;

        $CounterArray = [
          json_encode(['id' => $id, 'value' => '1', 'status' => 'locked'])
        ];
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

// just a helper until we implemented an endpoint
        $id = $this->iGetTheIdOfTheCounterWithName($name);

        $this->iGetTheValueOfTheCounterWithId($id);
    }


    /**
     * @When I get the value of the counter with ID :id
     */
    public function iGetTheValueOfTheCounterWithId($id)
    {

        $this->iSendARequest('GET', 'api/counters/value/' . $id);
        $this->printResponse();
    }

    /**
     * @Then the Id returned should be :arg1
     */
    public function theIdReturnedShouldBe($arg1)
    {
        throw new \Behat\Behat\Tester\Exception\PendingException();
    }

    /**
     * @When I reset the counter with Name :name
     */
    public function iResetTheCounterWithName($name)
    {

        // since we dont have endpoints for counter name directly
        // lets for now resolve name to id internally
        // just a helper until we implemented an endpoint
        $id = $this->iGetTheIdOfTheCounterWithName($name);

        $this->iResetTheCounterWithId($id);
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
        $CounterStringNode = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('PUT', $endpoint, $CounterStringNode);
        $this->printResponse();
        $this->theResponseCodeShouldBe('200');
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
     * @When I remove the counter with name :name
     */
    public function iRemoveTheCounterWithName($name)
    {

        // just a helper until we implemented an endpoint, might lead to wrong errors when id doesnt exist
        $id = $this->iGetTheIdOfTheCounterWithName($name);
        $this->iRemoveTheCounterWithId($id);
    }

    /**
     * @When I remove the counter with id :id
     */
    public function iRemoveTheCounterWithId($id)
    {
        $endpoint = '/api/counters/' . $id;
        $this->iSetHeaderWithValue('Content-Type', 'application/json');

        $this->accessHeader = 'Bearer ' . $this->accessToken;
        $this->iSetHeaderWithValue('Authorization', $this->accessHeader);

        $this->iSetHeaderWithValue('Accept', 'application/json');
        $CounterArray = [
          json_encode(['value' => 0])
        ];
        $CounterStringNode = new PyStringNode($CounterArray, 1);
        $this->iSendARequestWithBody('DELETE', $endpoint, $CounterStringNode);
        $this->printResponse();
    }

    /**
     * @Given a counter with name :name has been set
     */
    public function aCounterWithNameHasBeenSet($name)
    {
        $id = '1111111';
        $value = 1111111;
        $this->aCounterWithIdAndAValueOfWasAddedToTheCollection(
            $name,
            $id,
            $value
        );
    }

    /**
     * @Given a counter :name has been set
     */
    public function aCounterHasBeenSet($name)
    {
        $this->aCounterWithValueOfWasAddedToTheCollection($name, 0);
    }

    /**
     * creates counter via webapi
     * will mark counter for removal
     * verifies creation was successful
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
        $this->printResponse();
        $this->theResponseCodeShouldBe('201');

        // get the counter we added to db and remember it so we can delete it later
        $this->db = $this->app->getContainer()->get('pdo');
        $this->sqlManager = $this->app->getContainer()->get('counter_mapper');
        $this->counterRepository = $this->app->getContainer()
          ->get('counter_repository');
        $this->counterName = new CounterName($name);
        // if we have a counter mark it for cleanup after scenario
        if ($counter = $this->counterRepository->getCounterByName($this->counterName)) {
            $this->counters[] = $counter;
        }
    }

    /**
     * creates counter via webapi
     * will mark counter for removal
     * verifies response code
     * @When I set a counter with name :name
     */

    public function iSetACounterWithName($name)
    {
        try {
            $endpoint = '/api/counters/';
            // send a POST request to the endpoint with the counter values in the body
            $newCounterArray = [
              json_encode([
                'name' => $name,
                'value' => '0'
              ])
            ];
            $newCounterjsonString = new PyStringNode($newCounterArray, 1);
            $this->iSetHeaderWithValue('Content-Type', 'application/json');

            $this->accessHeader = 'Bearer ' . $this->accessToken;
            $this->iSetHeaderWithValue('Authorization', $this->accessHeader);

            $this->iSetHeaderWithValue('Accept', 'application/json');
            $this->iSendARequestWithBody(
                'POST',
                $endpoint,
                $newCounterjsonString
            );
            $this->printResponse();
            $this->theResponseCodeShouldBe('201');
        } catch (Exception $e) {
            $error = ['message' => $e->getMessage()];

            return $error;
        }
        $this->counterRepository = $this->app->getContainer()
          ->get('counter_repository');
        $this->counterName = new CounterName($name);
        // if we have a counter mark it for cleanup after scenario
        if ($counter = $this->counterRepository->getCounterByName($this->counterName)) {
            $this->counters[] = $counter;
        }
    }
}
