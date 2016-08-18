Feature: create a new counter
  As an apiuser
  in order to know what I am counting
  I need to be able to create a new counter with a name and default value of '0'

  @domain @web @adminui
  Scenario: create a new counter
    Given no counter "onecounter" has been set
    When I set a counter with name "onecounter"
    Then I can get the value of the counter with Name "onecounter"
    And I can get the value of the counter with Name "onecounter"
    And the value returned should be 0

    # Scenario: creating new counter if name already taken
    # Scenario: creating new counter with invalid name
    # Scenario: creating default counter