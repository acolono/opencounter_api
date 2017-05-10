Feature: Administer oauth2 clients
  As an admin
  in order to give people access to the api
  i want to manage oauth_clients


  Scenario: admin views empty list
    #Given I am authenticated with Admin
    When I look at the list of clients
    Then I should see "All Clients"
    And I should not see "democlient"


  Scenario: admin creates client via ui
    #Given I am authenticated with Admin
    When I add a new oauth2_client "democlient"
    And I look at the list of clients
    Then I should see "democlient"
