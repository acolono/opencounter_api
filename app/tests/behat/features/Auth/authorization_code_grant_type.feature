#@authorization_code_grant_type
#Feature: OAuth2 Token Grant Authorization Code
#
#  Scenario: Without authorization code
#    When I create oauth2 request
#    And I add the request parameters:
#        | grant_type | authorization_code |
#    And I send a access token request
#    Then the response status code is 400
#    And the response has a "error" property and it is equals "invalid_request"
#    And the response has a "error_description" property
#
#  Scenario: Invalid authorization code
#    When I create oauth2 request
#    And I add the request parameters:
#        | grant_type | authorization_code |
#        | client_id   | no       |
#        | client_secret   | bar      |
#    And I send a access token request
#    Then the response status code is 400
#    And the response has a "error" property and it is equals "invalid_client"
#    And the response has a "error_description" property
#
#  Scenario: Token Granted via auth code
#    When I create oauth2 request
#    And I add the request parameters:
#        | grant_type | authorization_code   |
#    And I add client credentials
#    And I send a access token request
#    Then the response status code is 200
#    And the response is oauth2 format
#    And the response has a "access_token" property
#    And the response has a "token_type" property and it is equals "Bearer"
#    And the response has a "refresh_token" property
#    And the response has a "expires_in" property and its type is numeric
#    And the response has a "scope" property
