Feature: edit one counter
  As a developer
  in order to /display the counter value
  i need to be able to /get the counter value/ from api

  GET /counters/1/value
@domain @web
  Scenario: increment the value for a single counter in the collection
    Given a counter with id "1" and a value of "1" was added to the collection
    When I increment the value of the counter with id 1
    Then the value returned should be 2
  @domain @web
  Scenario: lock a single counter in the collection and try to increment it
    Given a counter with id "1" and a value of "1" was added to the collection
    When I lock the counter with id 1
    And I increment the value of the counter with id 1
    Then I should see an error
    And the value returned should be 1

  #Scenario: unlock a single locked counter in the collection and increment it
  #Scenario: unlock a single unlocked counter in the collection and increment it