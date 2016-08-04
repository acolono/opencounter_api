<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\WebApiExtension\Context\WebApiContext;

use Pavlakis\Slim\Behat\Context\App;
use Pavlakis\Slim\Behat\Context\KernelAwareContext;

use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Domain\Model\Counter\CounterName;
use OpenCounter\Domain\Model\Counter\Counter;

/**
 * Defines application features from the specific context.
 */
class OpenCounterWebApiContext extends WebApiContext implements Context, SnippetAcceptingContext, KernelAwareContext
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
      $this->counter = array();
    }

  /**
   * @AfterScenario @web
   */
  public function cleanDB(\Behat\Behat\Hook\Scope\AfterScenarioScope $scope)
  {

    $this->db = $this->app->getContainer()->get('db');
    $this->sqlManager = new OpenCounter\Infrastructure\Persistence\Sql\SqlManager($this->db);
    $this->counterRepository = new \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlCounterRepository($this->sqlManager);

    if (isset($this->counter) && is_array($this->counter)) {
      echo 'removing testing counters';

      // foreach $created_counters as counter delete counter
      foreach ($this->counter as $counter) {
        $this->counterRepository->remove($counter);
      }
    }
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

      // TODO: need a counter object we can delete after scenario duplicating domain tests here for now
      $this->counterName = new CounterName($name);
      $this->counterId = new CounterId($id);
      $this->counterValue = new CounterValue($value);
      $counter = new Counter($this->counterName, $this->counterId, $this->counterValue, 'passwordplaceholder');

      $this->counter[] =$counter;



        // since we are using this as a given step we can make sure it was added successfully within this step
      $this->theResponseShouldContain('id');

      $this->theResponseCodeShouldBe('201');

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
      $this->theResponseShouldContain($arg1);
    }

    /**
     * @When I increment the value of the counter with ID :id
     */
    public function iIncrementTheValueOfTheCounterWithId($id)
    {
      $endpoint = '/api/v1/counters/' . $id . '/passwordplaceholder';

      $CounterArray = array(
        json_encode(array('value' => '+1'))
      );
//      [$rowLineNumber => [$val1, $val2, $val3]]
      $CounterjsonString = new PyStringNode($CounterArray, 1);
      $this->iSendARequestWithBody('PUT', $endpoint, $CounterjsonString);
      $this->printResponse();

    }

    /**
     * @When I lock the counter with ID :id
     */
    public function iLockTheCounterWithId($id)
    {
      $endpoint = '/api/v1/counters/' . $id;

      $CounterArray = array(
        json_encode(array('status' => 'locked'))
      );
//      [$rowLineNumber => [$val1, $val2, $val3]]
      $CounterjsonString = new PyStringNode($CounterArray, 1);
      $this->iSendARequestWithBody('PATCH', $endpoint, $CounterjsonString);
    }

    /**
     * @Then I should see an error :message
     */
    public function iShouldSeeAnError($message)
    {
      $errormesage = array('message' => $message);
      $ErrorString = new PyStringNode($errormesage, 1);
      $this->theResponseShouldContain($ErrorString);
    }




    /**
     * @When I get the value of the counter with ID :id
     */
    public function iGetTheValueOfTheCounterWithId($id)
    {
      $this->iSendARequest('GET', "api/v1/counters/$id/value");

    }

    /**
     * @When I reset the counter with ID :id
     */
    public function iResetTheCounterWithId($id)
    {
      $endpoint = '/api/v1/counters/' . $id;

      $CounterArray = array(
        json_encode(array('value' => 0))
      );
//      [$rowLineNumber => [$val1, $val2, $val3]]
      $CounterjsonString = new PyStringNode($CounterArray, 1);
      $this->iSendARequestWithBody('PATCH', $endpoint, $CounterjsonString);
    }


}
