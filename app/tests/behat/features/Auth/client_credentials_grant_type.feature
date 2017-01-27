@client_credentials_grant_type
Feature: OAuth2 Token Grant Password

  Scenario: Without client credentials
    When I create oauth2 request
#    And I add the request parameters:
#        | grant_type | client_credentials |
    And I send a access token request
    Then the response status code is 400
    And the response has a "error" property and it is equals "invalid_request"
    And the response has a "error_description" property

  Scenario: Invalid client credentials
    When I create oauth2 request
    And I add the request parameters:
        | grant_type | client_credentials |
        | client_id   | no       |
        | client_secret   | bar      |
    And I send a access token request
    Then the response status code is 400
    And the response has a "error" property and it is equals "invalid_client"
    And the response has a "error_description" property

  Scenario: Token Granted
    When I create oauth2 request
    And I add the request parameters:
        | grant_type | client_credentials   |
    And I add resource owner credentials
    And I send a access token request
    Then the response status code is 200
    And the response is oauth2 format
    And the response has a "access_token" property
    And the response has a "token_type" property and it is equals "Bearer"
    And the response has a "refresh_token" property
    And the response has a "expires_in" property and its type is numeric
    And the response has a "scope" property
