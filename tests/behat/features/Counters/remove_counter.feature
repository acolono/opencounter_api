Feature: remove counter
  As a developer
  in order to clean up after our tests
  I need to be able to delete the existing counter

  @domain @web
  Scenario: remove existing counter by id
    Given a counter with id "1CE05088-ED1F-43E9-A415-3B3792655A9B" has been set
    When I remove the counter with id "1CE05088-ED1F-43E9-A415-3B3792655A9B"
    Then no counter with id "1CE05088-ED1F-43E9-A415-3B3792655A9B" has been set

  @domain @web
  Scenario: remove existing counter by name
    Given a counter with name "onecounter" has been set
    When I remove the counter with id "onecounter"
    Then no counter with id "onecounter" has been set