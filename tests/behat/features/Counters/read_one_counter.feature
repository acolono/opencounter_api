Feature: read one counter
As an /api user/,
in order to /display the counter value
i need to be able to /get the counter value/ from api

GET /counters/1/value

  Scenario: Getting the value for a single counter in the collection
    Given a counter with ID "1" and a value of "1" was added to the collection
    When I get the value of the counter with ID 1
    Then the value returned should be 1
