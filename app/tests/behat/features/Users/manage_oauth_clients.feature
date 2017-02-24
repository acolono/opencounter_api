Feature: administrate oauth2 clients
  in order to give people access to the api
  as admin
  i want to manage oauth_clients

  @adminui
  @scenario: admin creates client via ui
    Given I am authenticated with Admin
    When I add a new oauth2_client "democlient"
    And I look at the list of clients
    Then I should see "democlient"

