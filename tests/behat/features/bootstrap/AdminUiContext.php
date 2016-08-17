<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;

use OpenCounter\Domain\Model\Counter\CounterId;
use OpenCounter\Domain\Model\Counter\CounterValue;
use OpenCounter\Domain\Model\Counter\Counter;

/**
 * Defines application features from the specific context.
 */
class AdminUiContext implements Context, SnippetAcceptingContext
{


    /**
     * @Given no counter :arg1 has been set
     */
    public function noCounterHasBeenSet($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When I set a counter with name :arg1
     */
    public function iSetACounterWithName($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then I can get the value of the counter with Name :arg1
     */
    public function iCanGetTheValueOfTheCounterWithName($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then the value returned should be :arg1
     */
    public function theValueReturnedShouldBe($arg1)
    {
        throw new PendingException();
    }
}
