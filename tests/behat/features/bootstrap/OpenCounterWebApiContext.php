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
class OpenCounterWebApiContext extends WebApiContext implements Context, SnippetAcceptingContext
{
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

    }

    /**
     * @Given a counter with ID :id and a value of :value was added to the collection
     */
    public function aCounterWithIdAndAValueOfWasAddedToTheCollection($id, $value)
    {
      $this->counterID = new CounterId($id);
      $this->counterValue = new CounterValue($value);
      $this->counter = new Counter($this->counterID, $this->counterValue, 'passwordplaceholder');
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
      throw new PendingException();
    }

    /**
     * @When I increment the value of the counter with ID :id
     */
    public function iIncrementTheValueOfTheCounterWithId($id)
    {
      throw new PendingException();

    }

    /**
     * @When I lock the counter with ID :id
     */
    public function iLockTheCounterWithId($id)
    {
      throw new PendingException();
    }

    /**
     * @Then I should see an error
     */
    public function iShouldSeeAnError()
    {
      throw new PendingException();
    }




    /**
     * @When I get the value of the counter with ID :arg1
     */
    public function iGetTheValueOfTheCounterWithId($arg1)
    {
      $this->iSendARequest('GET', "172.17.0.5/test");
    }

    /**
     * @When I reset the counter with ID :arg1
     */
    public function iResetTheCounterWithId($arg1)
    {
      throw new PendingException();
    }


}
