Feature: when i am not authenticated with valid access token i get appropriate error messages. i get access if i send authorized requests (use valid access token)

#  Scenario: unauthorized attempt to access admin routes
#
#  Scenario: Authorized attempt to access admin routes
#
  Scenario: Authorized attempt to access counter routes
    Given I have a valid access token
#
#  Scenario: Unauthorized attempt to access counter routes