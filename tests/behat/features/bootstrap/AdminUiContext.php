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
     * @AfterScenario
     */
    public function cleanDB(\Behat\Behat\Hook\Scope\AfterScenarioScope $scope)
    {

        $this->db = $this->app->getContainer()->get('db');
        $this->sqlManager = new OpenCounter\Infrastructure\Persistence\Sql\SqlManager($this->db);
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
     * @Given no counter :name has been set
     */
    public function noCounterHasBeenSet($name)
    {

        // get the counter we added to db and remember it so we can delete it later
        $this->db = $this->app->getContainer()->get('db');
        $this->sqlManager = new OpenCounter\Infrastructure\Persistence\Sql\SqlManager($this->db);
        $counterRepository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlCounterRepository($this->sqlManager);

        $counter = $counterRepository->getCounterByName(new CounterName($name));

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

        $this->counters = array();

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
}
