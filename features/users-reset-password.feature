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
    #TODO Create a test to verify admin can login with new password

  @restContext
  Scenario: Request a new password when authenticated shall return a 403 response.
    Given database is clean
    And I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/reset_password_requests" with body:
    """
    {
      "email": "owner@example.org"
    }
    """
    Then the response status code should be 403
    And the JSON nodes should contain:
      | @context           | /api/contexts/error                       |
      | @type              | hydra:Error                               |
      | hydra:title        | An error occurred                         |
      | hydra:description  | Access Denied.                            |


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

  Scenario: Post a new password with the valid code shall return a 202 response.
    Given database is clean
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/new_password_requests" with body:
    """
    {
      "code" : "admin-renew-code",
      "password": "my-new-password"
    }
    """
    Then the response status code should be 202
    And the response should be empty

  Scenario: Post a new password with an expired code shall return a 400 response.
    Given database is clean
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/new_password_requests" with body:
    """
    {
      "code" : "inactive-renew-code",
      "password": "my-new-password"
    }
    """
    Then the response status code should be 400
    And the JSON nodes should contain:
      | @context           | /api/contexts/Error  |
      | @type              | hydra:Error          |
      | hydra:title        | An error occurred    |
      | hydra:description  | Expired code.        |

  Scenario: Post a new password with an non-valid code shall return a 404 response.
    Given database is clean
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/new_password_requests" with body:
    """
    {
      "code" : "bad-renew-code",
      "password": "my-new-password"
    }
    """
    Then the response status code should be 404
    And the JSON nodes should contain:
      | @context           | /api/contexts/Error                       |
      | @type              | hydra:Error                               |
      | hydra:title        | An error occurred                         |
      | hydra:description  | This code is not valid to change password |

  @restContext
  Scenario: Post a new password with the valid code when authenticated shall return a 403 response.
    Given database is clean
    And I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/new_password_requests" with body:
    """
    {
      "code" : "owner-renew-code",
      "password": "my-new-password"
    }
    """
    Then the response status code should be 403
    And the JSON nodes should contain:
      | @context           | /api/contexts/Error                       |
      | @type              | hydra:Error                               |
      | hydra:title        | An error occurred                         |
      | hydra:description  | Access Denied.                            |
