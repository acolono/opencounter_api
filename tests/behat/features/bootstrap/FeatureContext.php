<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\WebApiExtension\Context\WebApiContext;

use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Domain\Model\Counter\Counter;
/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct( )
    {

      //$this->catalogue = new CounterRepository($pdo);
    }

    /**
     * @Given a counter with ID :id and a value of :value was added to the collection
     */
    public function aCounterWithIdAndAValueOfWasAddedToTheCollection($id, $value)
    {
      $this->counterID = new CounterId($id);
      $this->counterValue = new CounterValue($value);
      $this->counter = new Counter($this->counterID, $this->counterValue, 'passwordplaceholder');
     // $this->counter->id = $id;
//      $this->counter->value = $value;
      //$this->catalogue->add($this->counter);
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
      if (!$arg1 = $this->counterValue) {
        throw new PHPUnit_Framework_Error_Notice;
      }
    }

    /**
     * @When I increment the value of the counter with ID :arg1
     */
    public function iIncrementTheValueOfTheCounterWithId($arg1)
    {
      $this->counter->increment();
    }

    /**
     * @When I lock the counter with ID :arg1
     */
    public function iLockTheCounterWithId($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then I should see an error about the locked counter
     */
    public function iShouldSeeAnErrorAboutTheLockedCounter()
    {
        throw new PendingException();
    }

    /**
     * @Then the value of the counter with ID :arg1 should be :arg2
     */
    public function theValueOfTheCounterWithIdShouldBe($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @When I get the value of the counter with ID :arg1
     */
    public function iGetTheValueOfTheCounterWithId($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When I reset the counter with ID :arg1
     */
    public function iResetTheCounterWithId($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When I read the Counter with ID :arg1
     */
    public function iReadTheCounterWithId($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then the value of the counter with id :arg1 should be :arg2
     */
    public function theValueOfTheCounterWithIdShouldBe2($arg1, $arg2)
    {
        throw new PendingException();
    }
}
