Feature: edit one counter
  As an apiuser
  in order to change the state or the value of the counter
  I need to be able to send a patch request to /counters/route from api

  PATCH
    /counters/1/value
    increment value of counter

    /counters/1/state
    change state of counter (active, disabled, locked)

  @domain @web
  Scenario: increment the value for a single counter
    Given a counter "onecounter" with a value of "1" has been set
    When I increment the value of the counter with name "onecounter"
    And I get the value of the counter with name "onecounter"
    Then the value returned should be 2

  @domain @web
  Scenario: lock a single counter and try to increment it
    Given a counter "onecounter" with a value of "1" has been set
    When I lock the counter with name "onecounter"
    And I increment the value of the counter with name "onecounter"
    Then I should see an error "counter with name onecounter is locked"
    When I get the value of the counter with name "onecounter"
    And the value returned should be 1

  #Scenario: unlock a single locked counter in the collection and increment it
  #Scenario: unlock a single unlocked counter in the collection and increment it