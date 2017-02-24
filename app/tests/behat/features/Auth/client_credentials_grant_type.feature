@client_credentials_grant_type
Feature: OAuth2 Token Grant ClientCredentials

  Scenario: Without client credentials
    When I create oauth2 request
    And I add the request parameters:
        | grant_type | client_credentials |
    And I send a access token request
    Then the response status code is 400
    And the response has a "error" property and it is equals "invalid_client"
    And the response has a "error_description" property

  Scenario: Invalid client credentials
    When I create oauth2 request
    And I add the request parameters:
      | grant_type    | client_credentials |
      | client_id     | no                 |
      | client_secret | bar                |
    And I send a access token request
    Then the response status code is 400
    And the response has a "error" property and it is equals "invalid_client"
    And the response has a "error_description" property

  Scenario: Token Granted to client_credentials in db
    # TODO: pick how we want to ensure the user we are authenticating as exists in the database (http://bshaffer.github.io/oauth2-server-php-docs/storage/pdo/) disabling this test until we decide
##    Given oauth client exists in database:
##    Given I reseed the user migration
#    When I create oauth2 request
#    And I add the request parameters:
#      | grant_type | client_credentials |
#    And I add client credentials
#    And I send a access token request
#    Then the response status code is 200
#    And the response is oauth2 format
#    And the response has a "access_token" property
#    And the response has a "token_type" property and it is equals "Bearer"
#    #@see http://tools.ietf.org/html/rfc6749#section-4.4.3
#    # vendor/bshaffer/oauth2-server-php/src/OAuth2/GrantType/ClientCredentials.php
##    And the response has a "refresh_token" property
#    And the response has a "expires_in" property and its type is numeric
#    And the response has a "scope" property
