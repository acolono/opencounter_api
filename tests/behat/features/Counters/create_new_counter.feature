Feature: create a new counter
  As an apiuser
  in order to know what I am counting
  I need to be able to create a new counter with a name and default value of '0'

  @domain @web
  Scenario: create a new counter
    Given there is no counter "onecounter"
    When I create a counter with name "onecounter"
    Then there is a counter "onecounter" with a value of "0"