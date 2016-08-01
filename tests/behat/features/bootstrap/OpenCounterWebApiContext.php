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
     * @Given a counter :name with ID :id and a value of :value was added to the collection
     */
    public function aCounterWithIdAndAValueOfWasAddedToTheCollection($name, $id, $value)
    {

      throw new PendingException('TODO describe how to send POST request with counter through the counter route using existing webapi vocabulary (behat -dl) where possible and cosider https://akrabat.com/testing-slim-framework-actions/accessing although that should be covered by https://github.com/slimphp/Slim/tree/3.x/tests');
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
//      throw new PendingException();
      $this->theResponseShouldContain($arg1);
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
//      $this->iSendARequest('GET', "172.17.0.5/test");
      throw new PendingException();
    }

    /**
     * @When I reset the counter with ID :arg1
     */
    public function iResetTheCounterWithId($arg1)
    {
      throw new PendingException();
    }


}
