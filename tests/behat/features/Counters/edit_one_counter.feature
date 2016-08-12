Feature: edit one counter
  As an /api user/,
  in order to /display the counter value
  i need to be able to /get the counter value/ from api

  GET /counters/1/value

  Scenario: increment the value for a single counter in the collection
    Given a counter with ID "1" and a value of "1" was added to the collection
    When I increment the value of the counter with ID 1
    Then the value returned should be 2

  Scenario: lock a single counter in the collection and try to increment it
    Given a counter with ID "1" and a value of "1" was added to the collection
    When I lock the counter with ID 1
    And I increment the value of the counter with ID 1
    Then I should see an error about the locked counter
    And the value of the counter with ID 1 should be 1

  #Scenario: unlock a single locked counter in the collection and increment it
  #Scenario: unlock a single unlocked counter in the collection and increment it