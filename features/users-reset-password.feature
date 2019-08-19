# features/users-reset-password.feature
Feature: Users reset password feature
  Scenario: Request a new password shall return a 202 response.
    Given database is clean
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/reset_password_requests" with body:
    """
    {
      "email": "owner@example.org"
    }
    """
    Then the response status code should be 202
    And the response should be empty

  Scenario: Request a new password for a non-existant user shall return a 404 response.
    Given database is clean
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/reset_password_requests" with body:
    """
    {
      "email": "non-existent-user@example.org"
    }
    """
    Then the response status code should be 404
    And the JSON nodes should contain:
      | @context           | /api/contexts/error |
      | @type              | hydra:Error         |
      | hydra:title        | An error occurred   |
      | hydra:description  | Email not found     |
