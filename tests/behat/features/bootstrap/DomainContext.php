<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Model\Counter\CounterName;
use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Domain\Model\Counter\Counter;

use Pavlakis\Slim\Behat\Context\App;
use Pavlakis\Slim\Behat\Context\KernelAwareContext;

/**
 * Defines application features from the specific context.
 */
class DomainContext implements Context, SnippetAcceptingContext, KernelAwareContext
{
    use App;

    /**
     * @var bool
     */
    private $error;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        // in reality we will get the builder from container
        $this->logger = new \Monolog\Logger('domaincontext behat');
        $this->counter_factory = new \OpenCounter\Infrastructure\Factory\Counter\CounterFactory();
        // Note, using inmemory repo for testing domain layer, not actual db.
        // TODO: figure out a better place for these fixture thingies
        $counters = [];
        $counters[] = new Counter(
            new CounterId('1CE05088-ED1F-43E9-A415-3B3792655A9B'),
            new CounterName('onecounter'), new CounterValue(2), 'active',
            'passwordplaceholder'
        );
        $counters[] = new Counter(
            new CounterId('8CE05088-ED1F-43E9-A415-3B3792655A9B'),
            new CounterName('twocounter'), new CounterValue(2), 'active',
            'passwordplaceholder'
        );
        $counters[] = new Counter(
            new CounterId('62A0CEB4-0403-4AA6-A6CD-1EE808AD4D23'),
            new CounterName('test'), new CounterValue(0), 'locked',
            'passwordplaceholder'
        );
        $counters[] = new Counter(
            new CounterId('62A0CEB4-4575-4AA6-FD76-1EE808AD4D23'),
            new CounterName('onecounter'), new CounterValue(1), 'disabled',
            'passwordplaceholder'
        );
        $this->counter_repository = new \OpenCounter\Infrastructure\Persistence\InMemory\Repository\Counter\InMemoryCounterRepository($counters);
        $this->counterBuildService = new \OpenCounter\Http\CounterBuildService($this->counter_repository,
            $this->counter_factory, $this->logger);
    }

    /**
     * @Given a counter :name with ID :id and a value of :value was added to the collection
     */
    public function aCounterWithIdAndAValueOfWasAddedToTheCollection(
        $name,
        $id,
        $value
    )
    {
        $this->counterName = new CounterName($name);
        $this->counterId = new CounterId($id);
        $this->counterValue = new CounterValue($value);
        // lets use the factory to create the counter here, but not bother with using the build service
        $this->counter = $this->counter_factory->build($this->counterId, $this->counterName, $this->counterValue, 'active', 'passworplaceholder');


    }

    /**
     * @Given a counter( with name) :name has been set
     */
    public function aCounterhasBeenSet($name)
    {
        $this->counterName = new CounterName($name);
        $this->counterId = new CounterId();
        $this->counterValue = new CounterValue(0);

        // lets use the factory to create the counter here, but not bother with using the build service
        $this->counter = $this->counter_factory->build($this->counterId, $this->counterName, $this->counterValue, 'active', 'passwordplaceholder');

    }

    /**
     * @Given a counter :name with a value of :value has been set
     */
    public function aCounterWithAValueOfWasAddedToTheCollection($name, $value)
    {
        $this->counterName = new CounterName($name);
        $this->counterId = new CounterId();
        $this->counterValue = new CounterValue($value);
        // lets use the factory to create the counter here, but not bother with using the build service
        $this->counter = $this->counter_factory->build($this->counterId, $this->counterName, $this->counterValue, 'active', 'passwordplaceholder');

    }

    /**
     * @Then the value returned should be :arg1
     */
    public function theValueReturnedShouldBe($arg1)
    {
        if (!$arg1 == $this->counter->getValue()) {
            throw new \Exception('value not equal');
        }
    }

    /**
     * @When I increment the value of the counter with ID :id
     */
    public function iIncrementTheValueOfTheCounterWithId($id)
    {
        try {
            $incremented = $this->counter->incrementValue();
        } catch (Exception $e) {
            $this->error = true;
        }

    }

    /**
     * @When I increment the value of the counter with name :name
     */
    public function iIncrementTheValueOfTheCounterWithName($name)
    {

        try {
            $increment = new CounterValue(1);
            $incremented = $this->counter->increaseCount($increment);
        } catch (Exception $e) {
            $this->error = true;
        }

    }

    /**
     * @When I lock the counter with ID :id
     */
    public function iLockTheCounterWithId($id)
    {
        $this->counter->lock();
    }

    /**
     * @When I lock the counter with Name :name
     */
    public function iLockTheCounterWithName($name)
    {
        $this->counter->lock();
    }

    /**
     * @Then I should see an error :message
     */
    public function iShouldSeeAnError($message)
    {
        if ($this->error !== true) {
            throw new \Exception('Error not found');
        }
    }


    /**
     * @When I (can )get the value of the counter with ID :arg1
     */
    public function iGetTheValueOfTheCounterWithId($arg1)
    {
        $this->counter->getValue();
    }


    /**
     * @When I (can )get the value of the counter with Name :name
     */
    public function iGetTheValueOfTheCounterWithName($name)
    {
        $this->counter->getValue();
    }

    /**
     * @When I reset the counter with ID :arg1
     */
    public function iResetTheCounterWithId($arg1)
    {
        $this->counter->reset();
    }

    /**
     * @When I (can )reset the counter with Name :name
     */
    public function iResetTheCounterWithName($name)
    {
        $newValue = new CounterValue(0);
        $this->counter->resetValueTo($newValue);
    }

    /**
     * @When I set a counter with name :name
     */
    public function iSetACounterWithName($name)
    {

        $uri = \Slim\Http\Uri::createFromString('http://slimapi.opencounter.docker');
        $headers = new \Slim\Http\Headers();
        $cookies = [];
        $serverParams = [];
        $body = new \Slim\Http\Body(fopen('php://temp', 'r+'));
        $request = new \Slim\Http\Request('GET', $uri, $headers, $cookies,
            $serverParams, $body);
        $args = ['name' => $name, 'value' => 0];
// now thest the build service just in case
        $this->counter = $this->counterBuildService->execute($request, $args);
        // cannot save in memory repository since its not persistent, so not testing this?
        //$counter_repository->save($counter);
    }

    /**
     * @Given no counter :name has been set
     */
    public function noCounterHasBeenSet($name)
    {

        try {
            $this->counter = $this->counter_repository->getCounterByName($this->counterName);
        } catch (Exception $e) {
            $this->error = true;
        }
        return $this->error;

    }

    /**
     * @When I remove the counter with id :id
     */
    public function iRemoveTheCounterWithId($id)
    {
        try {
            $CounterId = new CounterId($id);
            $removed = $this->counter_repository->removeCounterById($CounterId);
        } catch (Exception $e) {
            $this->error = true;
        }
        return $this->error;
    }

    /**
     * @When I remove the counter with name :name
     */
    public function iRemoveTheCounterWithName($name)
    {
// note we are ignoringpassed name and using $this->counterName
        try {
            $removed = $this->counter_repository->removeCounterById($this->counterName);
        } catch (Exception $e) {
            $this->error = true;
        }
        return $this->error;
    }


}
