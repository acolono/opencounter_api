Feature: when i am not authenticated i get blocked. i get access if i send authorized requests

  Scenario: unauthorized attempt to access admin routes

  Scenario: Authorized attempt to access admin routes

  Scenario: Authorized attempt to access counter routes
    Given I have a valid access token

  Scenario: Unauthorized attempt to access counter routes