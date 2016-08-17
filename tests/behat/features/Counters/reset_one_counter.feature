Feature: reset one counter
  As an apiuser
  in order to remove the current counter value (start over)
  i need to be able to reset the counter value and state to default from api

  PUT /counters/1
  given a Counter, it is possible to replace all its properties, and if that counter doesn’t exist it should be created.

  @domain @web
  Scenario: Resetting the first counter
    Given a counter "onecounter" with a value of "1" has been set
    When I reset the counter with name "onecounter"
    And I get the value of the counter with name "onecounter"
    Then the value returned should be "0"