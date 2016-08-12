Feature: reset one counter
As an /api user/,
in order to /display the counter value
i need to be able to /get the counter value/ from api

PUT /counters/1

  Scenario: Resetting the first counter
    Given a counter with ID "1" and a value of "1" was added to the collection
    When I reset the counter with ID 1
    And I read the Counter with ID "1"
    Then the value of the counter with id "1" should be "0"
