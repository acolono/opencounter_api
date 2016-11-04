<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\MinkContext;

use Pavlakis\Slim\Behat\Context\App;
use Pavlakis\Slim\Behat\Context\KernelAwareContext;

use OpenCounter\Domain\Model\Counter\CounterName;
use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Domain\Model\Counter\Counter;

class AdminUiContext extends MinkContext implements Context, SnippetAcceptingContext, KernelAwareContext
{
    use App;

    /**
     * AdminUiContext constructor.
     */
    public function __construct()
    {
        $this->counters = array();
    }


    /**
     * @AfterScenario
     */
    public function cleanDB(\Behat\Behat\Hook\Scope\AfterScenarioScope $scope)
    {

        $this->db = $this->app->getContainer()->get('db');
        $this->sqlManager = $this->app->getContainer()->get('counter_mapper');
        $this->counterRepository = $this->app->getContainer()
            ->get('counter_repository');

        if (isset($this->counters) && is_array($this->counters)) {
            echo 'removing testing counters';

            // foreach $created_counters as counter delete counter
            foreach ($this->counters as $counter) {
                $this->counterRepository->remove($counter);
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
        $this->visitPath('/admin/content/add');
//        [$rowLineNumber => [$val1, $val2, $val3]]
//        id|name|label|value|placeholder
        $fields = new \Behat\Gherkin\Node\TableNode(array(
            array('name', $name),
            array('status', 'active'),
            array('value', 0)
        ));
        $this->fillFields($fields);
        $this->pressButton('submit');

        // get the counter we added to db and remember it so we can delete it later
        $this->db = $this->app->getContainer()->get('db');
        $this->sqlManager = new OpenCounter\Infrastructure\Persistence\Sql\SqlManager($this->db);
        $counterRepository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlCounterRepository($this->sqlManager);

        $this->counterName = new CounterName($name);
        $counter = $counterRepository->getCounterByName($this->counterName);

        $this->counters[] = $counter;
    }
    /**
     * @When I set a counter with name :name and value :value
     */
    public function iSetACounterWithNameAndValue($name, $value)
    {
        $this->visitPath('/admin/content/add');
//        [$rowLineNumber => [$val1, $val2, $val3]]
//        id|name|label|value|placeholder
        $fields = new \Behat\Gherkin\Node\TableNode(array(
            array('name', $name),
            array('status', 'active'),
            array('value', $value)
        ));
        $this->fillFields($fields);
        $this->pressButton('submit');

        // get the counter we added to db and remember it so we can delete it later
        $this->db = $this->app->getContainer()->get('db');
        $this->sqlManager = new OpenCounter\Infrastructure\Persistence\Sql\SqlManager($this->db);
        $counterRepository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlCounterRepository($this->sqlManager);

        $this->counterName = new CounterName($name);
        $counter = $counterRepository->getCounterByName($this->counterName);

        $this->counters[] = $counter;
    }

    /**
     * @Then I can get the value of the counter with Name :name
     */
    public function iCanGetTheValueOfTheCounterWithName($name)
    {
        $this->visitPath('/admin/counters/' . $name);
        $this->assertElementContainsText('h1', 'View Counter ' . $name);
        $this->assertElementOnPage('li.counter__value');

    }

    /**
     * @Then the value returned should be :value
     */
    public function theValueReturnedShouldBe($value)
    {
        $this->assertElementContainsText('li.counter__value', $value);
    }

    /**
     * @When I remove the counter with id :arg1
     */
    public function iRemoveTheCounterWithId($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then no counter with id :arg1 has been set
     */
    public function noCounterWithIdHasBeenSet($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given a counter :name with a value of :value has been set
     */
    public function aCounterWithAValueOfHasBeenSet($name, $value)
    {

        // TODO: like this it will fail if none was set, but probably we want to use this to make sure one is set.
        // TODO: currently still ignoring the value because we arent creating the counter
        // so add the counter to db here?
// lets create the counter here instead of assuming it exists
        $this->iSetACounterWithNameAndValue($name, $value);

    }

    /**
     * @Given there is a counter :name with a value of :value in the collection
     */
    public function aCounterWithAValueOfexists($name, $value)
    {


        // get the counter we added to db and remember it so we can delete it later
        $counterRepository = $this->app->getContainer()->get('counter_repository');
        $counter = $counterRepository->getCounterByName(new CounterName($name));
        // mark for post scenario deletion
        $this->counters[] = $counter;
        // if we get a counter something is wrong
        if (!$counter) {
            throw new \Exception('something is wrong, no counter by that name was found');
        }

    }

    /**
     * @When I increment the value of the counter with name :name
     */
    public function iIncrementTheValueOfTheCounterWithName($name)
    {
        throw new PendingException();
    }

    /**
     * @When I get the value of the counter with name :name
     */
    public function iGetTheValueOfTheCounterWithName($name)
    {
        $this->iCanGetTheValueOfTheCounterWithName($name);
    }

    /**
     * @When I lock the counter with name :name
     */
    public function iLockTheCounterWithName($name)
    {
        throw new PendingException();
    }

    /**
     * @Then I should see an error :arg1
     */
    public function iShouldSeeAnError($arg1)
    {
        throw new PendingException();
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
        $request = new \Slim\Http\Request('GET', $uri, $headers, $cookies,
            $serverParams, $body);
        $args = ['name' => $name, 'id' => $id, 'value' => 0];
// now thest the build service just in case
        // cant test build service without request
        $counter = $this->app->getContainer()->get('counter_build_service')->execute($request, $args);
// still need to use repository to save counter and add it to counters array for post scenario deletion
        $this->app->getContainer()->get('counter_repository')->save($counter);
        $this->counters[] = $counter;

    }

    /**
     * @When I reset the counter with name :arg1
     */
    public function iResetTheCounterWithName($arg1)
    {
        throw new PendingException();
    }
}
