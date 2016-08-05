Feature: reset one counter
As a developer
in order to display the counter value
i need to be able to /get the counter value/ from api

PUT /counters/1
@domain @web
  Scenario: Resetting the first counter
    Given a counter "onecounter" with a value of "1" was added to the collection
    When I reset the counter with name "onecounter"
    And I get the value of the counter with name "onecounter"
    Then the value returned should be "0"
