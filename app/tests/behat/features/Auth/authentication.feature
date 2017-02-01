Feature: I can authenticate with credentials to become authorized to access counter routes via an access token.

#  Scenario: fail when trying to authenticate with nonexistant client credentials
#  Scenario: fail when trying to authenticate with wrong client credentials
#  Scenario: fail when trying to authenticate without credentials
#
#  Scenario: user exists in db and authenticates using client credentials
#    # we might as well just reseed the database instead of inserting as part of the test
#  Given oauth_client:
#  # not much use passing oauth details as context parameters if we want to insert users into the db during the test.
#  |client_id|client_secret|username|password|
#    When I create oauth2 request
#    And I add the request parameters:
#      | grant_type | client_credentials |
#    And I add resource owner credentials
#    And I send a access token request
#    Then the response status code is 200
#    And the response is oauth2 format
#    And the response has a "access_token" property
#    And the response has a "token_type" property and it is equals "Bearer"
#    And the response has a "refresh_token" property
#    And the response has a "expires_in" property and its type is numeric
#    And the response has a "scope" property

#  Scenario: user exists in db and authenticates using client credentials
#  Scenario: user exists in db and authenticates using user credentials
#  Scenario: user exists in db and authenticates using authorization code
#
#  Scenario: adding user to db