Feature: reset one counter
As an /api user/,
in order to /display the counter value
i need to be able to /get the counter value/ from api

PUT /counters/1

  Scenario: Resetting the first counter
    Given a counter with id "1" and a value of "1" was added to the collection
    When I reset the counter with id 1
    And I get the value of the counter with id 1
    Then the value returned should be "0"
