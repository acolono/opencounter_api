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
    public function __construct( )
    {
      //$this->counter_factory = $this->app->getContainer()->get('counter_factory');

      //$this->catalogue = new CounterRepository($pdo);
    }

    /**
     * @Given a counter :name with ID :id and a value of :value was added to the collection
     */
    public function aCounterWithIdAndAValueOfWasAddedToTheCollection($name, $id, $value)
    {
      $this->counterName = new CounterName($name);
      $this->counterId = new CounterId($id);
      $this->counterValue = new CounterValue($value);
      //$this->counter = $this->counter_factory->build($this->counterId, $this->counterName, $value, $password);

      $this->counter = new Counter($this->counterName, $this->counterId, $this->counterValue, 'passwordplaceholder');

    }

  /**
   * @Given a counter :name with a value of :value was added to the collection
   */
  public function aCounterWithAValueOfWasAddedToTheCollection($name, $value)
  {
    $this->counterName = new CounterName($name);
    $this->counterId = new CounterId();
    $this->counterValue = new CounterValue($value);
    //$counter = $this->counter_factory->build($this->counterId, $this->counterName, $value, $password);

    $this->counter = new Counter($this->counterId, $this->counterName, $this->counterValue, 'active', 'passwordplaceholder');
  }
//    /**
//     * @When I get the value of the counter with ID :id
//     */
//    public function iGetTheValueOfTheCounterWithId($id)
//    {
//
//
//// Fetch the counter from the database
////      $this->counter = $this->catalogue->findByCounterId($id);
//$this->counterValue = $this->counter->getValue();
//// Modify the Counter
//     // $counter->changeProperty('value', '3');
//
//// Persist the changes back to the database
//    //  $this->catalogue->update($counter);
//    }

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
      $incremented = $this->counter->increaseCount();
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
     * @When I get the value of the counter with ID :arg1
     */
    public function iGetTheValueOfTheCounterWithId($arg1)
    {
      $this->counter->getValue();
    }


    /**
     * @When I get the value of the counter with Name :name
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
     * @When I reset the counter with Name :name
     */
    public function iResetTheCounterWithName($name)
    {
      $newValue = new CounterValue(0);
      $this->counter->resetValueTo($newValue);
    }


}
