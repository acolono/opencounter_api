<?php
/*
 * Contains a contet to test admin ui
 */
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context\MinkContext;
use OpenCounter\Domain\Model\Counter\CounterName;
use Pavlakis\Slim\Behat\Context\App;
use Pavlakis\Slim\Behat\Context\KernelAwareContext;

/**
 * Class AdminUiContext
 */
class AdminUiContext extends MinkContext implements Context, SnippetAcceptingContext, KernelAwareContext
{

    use App;

    /**
     * AdminUiContext Constructor
     *
     * @param $parameters
     */
    public function __construct($parameters)
    {
        // Initialize your context here
        $this->parameters = $parameters;
        $this->baseUrl = $parameters['base_url'];
        $this->counters = [];
        $this->oauth2Clients = [];
    }

    /**
     * Clean db
     *
     * after each scenario
     *
     * @param \Behat\Behat\Hook\Scope\AfterScenarioScope $scope
     *
     * @AfterScenario
     *
     */
    public function cleanDB(\Behat\Behat\Hook\Scope\AfterScenarioScope $scope)
    {

        $this->db = $this->app->getContainer()->get('pdo');
        $this->sqlManager = $this->app->getContainer()->get('counter_mapper');
        $this->counterRepository = $this->app->getContainer()
          ->get('counter_repository');


        if (isset($this->counters) && is_array($this->counters) && !(empty($this->counters))) {
            echo 'removing testing counters';

            // foreach $created_counters as counter delete counter
            foreach ($this->counters as $counter) {
                $this->counterRepository->remove($counter);
            }
        }
        // Cleanup oauth clients created during tests
        if (isset($this->oauth2Clients) && is_array($this->oauth2Clients) && !(empty($this->oauth2Clients))) {
            echo 'removing testing oauth2Clients';
            $this->oauth2ClientRepository = $this->app->getContainer()
              ->get('oauth2_storage');

            // foreach $created_oauth2Clients as counter delete counter
            foreach ($this->oauth2Clients as $oauth2ClientId) {
                $this->oauth2ClientRepository->deleteClientById($oauth2ClientId);
            }
        }
    }

    /**
     * @Given a counter with name :name has been set
     */
    public function aCounterWithNameHasBeenSet($name)
    {
        $this->aCounterWithAValueOfHasBeenSet($name, 0);
    }

    /**
     * @Given a counter :name with a value of :value has been set
     */
    public function aCounterWithAValueOfHasBeenSet($name, $value)
    {
        // lets create the counter here instead of assuming it exists
        $this->iSetACounterWithNameAndValue($name, $value);
    }

    /**
     * @When I set a counter with name :name and value :value
     */
    public function iSetACounterWithNameAndValue($name, $value)
    {
        //      authenticate with basic auth
        $this->visitPath($this->baseUrl . '/admin/content/add');

        $fields = new \Behat\Gherkin\Node\TableNode([
          ['name', $name],
          ['status', 'active'],
          ['value', $value],
        ]);
        $this->fillFields($fields);
        $this->pressButton('submit');

        // get the counter we added to db and remember it so we can delete it later
        $this->db = $this->app->getContainer()->get('pdo');
        $this->sqlManager = new OpenCounter\Infrastructure\Persistence\Sql\SqlManager($this->db);
        $this->counterRepository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlCounterRepository($this->sqlManager);

        $this->counterName = new CounterName($name);
        // if we have a counter mark it for cleanup after scenario
        if ($counter = $counter = $this->counterRepository->getCounterByName($this->counterName)) {
            $this->counters[] = $counter;
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
     * @When I set a counter with name :name
     */
    public function iSetACounterWithName($name)
    {

        $this->visitPath($this->baseUrl . '/admin/content/add');

        $fields = new \Behat\Gherkin\Node\TableNode([
          ['name', $name],
          ['status', 'active'],
          ['value', 0],
        ]);
        $this->fillFields($fields);
        $this->pressButton('submit');

        // get the counter we added to db and remember it so we can delete it later
        $this->db = $this->app->getContainer()->get('pdo');
        $this->sqlManager = new OpenCounter\Infrastructure\Persistence\Sql\SqlManager($this->db);
        $counterRepository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlCounterRepository($this->sqlManager);

        $this->counterName = new CounterName($name);
        // if we have a counter mark it for cleanup after scenario
        if ($counter = $counter = $this->counterRepository->getCounterByName($this->counterName)) {
            $this->counters[] = $counter;
        }
    }

    /**
     * @Then the value returned should be :value
     */
    public function theValueReturnedShouldBe($value)
    {
        $this->assertElementContainsText('li.counter__value', $value);
    }

    /**
     * @Given there is a counter :name with a value of :value in the collection
     */
    public function aCounterWithAValueOfexists($name, $value)
    {

        // get the counter we added to db and remember it so we can delete it later
        $counterRepository = $this->app->getContainer()
          ->get('counter_repository');
        // if we have a counter mark it for cleanup after scenario
        if ($counter = $counter = $this->counterRepository->getCounterByName($this->counterName)) {
            $this->counters[] = $counter;
        }
        // if we get a counter something is wrong
        if (!$counter) {
            throw new \Exception('something is wrong, no counter by that name was found');
        }
    }

    /**
     * @When I get the value of the counter with name :name
     */
    public function iGetTheValueOfTheCounterWithName($name)
    {
        $this->iCanGetTheValueOfTheCounterWithName($name);
    }

    /**
     * @Then I can get the value of the counter with Name :name
     */
    public function iCanGetTheValueOfTheCounterWithName($name)
    {

        $this->visitPath($this->baseUrl . '/admin/counters/' . $name);
        $this->assertElementContainsText('h1', 'View Counter ' . $name);
        $this->assertElementOnPage('li.counter__value');
    }

    /**
     * @Given a counter with id :id has been set
     */
    public function aCounterWithIdHasBeenSet($id)
    {
        // TODO: we are duplicating the code in webapi context here
        // since we want to make sure the counter exists
        // and not have to add it through rest or the ui.
        //instead use build service and add to repository because that how to correctly create a counter
        // so think about accessing that context from admin ui context

        $name = 'democounter';
        $uri = \Slim\Http\Uri::createFromString('http://slimapi.opencounter.docker');
        $headers = new \Slim\Http\Headers();
        $cookies = [];
        $serverParams = [];
        $body = new \Slim\Http\Body(fopen('php://temp', 'r+'));
        $request = new \Slim\Http\Request(
          'GET',
          $uri,
          $headers,
          $cookies,
          $serverParams,
          $body
        );
        $args = ['name' => $name, 'id' => $id, 'value' => 0];
        // now test the build service just in case
        // cant test build service without request
        $counter = $this->app->getContainer()
          ->get('counter_build_service')
          ->execute($request, $args);
        // use repository to save counter and add it to counters array for post scenario deletion
        $this->app->getContainer()->get('counter_repository')->save($counter);
        $this->counters[] = $counter;
    }

    /**
     * @When I add a new oauth2_client :client_id
     */
    public function iAddANewOauthClient($client_id)
    {
        $this->visitPath($this->baseUrl . '/admin/clients/add');
        $fields = new \Behat\Gherkin\Node\TableNode([
          ['client_id', $client_id],
          ['user_id', 'exampleuserid'],
          ['client_secret', 'examplesecret'],
          ['redirect_uri', '/o2c.html'],
          ['scopes', 'read:counters'],
          ['grant_types', 'authorization_code implicit client_credentials'],
        ]);
        $this->fillFields($fields);
        $this->pressButton('submit');

        $this->oauth2Clients[] = $client_id;

    }

    /**
     * @When I look at the list of clients
     */
    public function iLookAtTheListOfClients()
    {
        $this->visitPath($this->baseUrl . '/admin/clients');

    }
}
