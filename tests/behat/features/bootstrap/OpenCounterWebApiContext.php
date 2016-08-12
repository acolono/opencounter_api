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
   * @BeforeSuite
   */
  public static function prepare(BeforeSuiteScope $scope)
  {
    // prepare system for test suite
    // before it runs
    // this will hold counters we create during tests so we can delete them from db

    $created_counters = array();

  }

  /**
   * @AfterScenario @web
   */
  public function cleanDB(AfterScenarioScope $scope)
  {
    // clean database after scenarios,
    // tagged with @web

    // TODO: foreach $created_counters as counter delete counter
  }
    /**
     * @Given a counter :name with ID :id and a value of :value was added to the collection
     */
    public function aCounterWithIdAndAValueOfWasAddedToTheCollection($name, $id, $value)
    {
      $endpoint = '/api/v1/counters/' . $id;
      // send a POST request to the endpoint with the counter values in the body
      $newCounterArray = array(
        json_encode(array('anId' => $id, 'name' => $name, 'value' => $value))
      );
//      [$rowLineNumber => [$val1, $val2, $val3]]
      $newCounterjsonString = new PyStringNode($newCounterArray, 1);
      $this->iSetHeaderWithValue('Content-Type', 'application/json');
      $this->iSetHeaderWithValue('Accept', 'application/json');
      $this->iSendARequestWithBody('POST', $endpoint, $newCounterjsonString);

$this->printResponse();



      // since we are using this as a given step we can make sure it was added successfully within this step
      $this->theResponseShouldContain('id');

      $this->theResponseCodeShouldBe('200');

      //make absolutely sure we added it successfully and our cleanup works
      $errormesage = array('message' => "counter with id $id already exists");
      $ErrorString = new PyStringNode($newCounterArray, 1);
      $this->theResponseShouldNotContain($ErrorString);
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
