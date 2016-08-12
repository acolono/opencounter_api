Feature: remove counter
  As a developer
  in order to clean up after our tests
  I need to be able to delete the existing counter

  @domain @web
  Scenario: remove existing counter
    Given there is a counter "onecounter"
    When I remove the counter with name "onecounter"
    Then there is no counter "onecounter".