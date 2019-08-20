# features/users-activation.feature
Feature: Users activation feature
  Scenario: Sending the good activation code without authentication shall activate account.
    Given database is clean
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/user_activation_requests" with body:
    """
    {
      "activation": "inactive-activation-code"
    }
    """
    Then the response status code should be 202
    And the response should be empty

  Scenario: Sending a bad activation code without authentication shall return an invalid error.
    Given database is clean
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/user_activation_requests" with body:
    """
    {
      "activation": "bad-activation-code"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context                 | /api/contexts/Error |
      | @type                    | hydra:Error         |
      | hydra:title              | An error occurred   |
      | hydra:description        | Bad activation code |

  @restContext
  Scenario: Sending a good activation code with authentication shall return an access denied error.
    Given database is clean
    And I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/user_activation_requests" with body:
    """
    {
      "activation": "inactive-activation-code"
    }
    """
    Then the response status code should be 403
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context                 | /api/contexts/Error |
      | @type                    | hydra:Error         |
      | hydra:title              | An error occurred   |
      | hydra:description        | Access Denied.      |


  @restContext
  Scenario: Sending a request with activation code to an already activated user shall return an error.
    Given database is clean
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/user_activation_requests" with body:
    """
    {
      "activation": "inactive-activation-code"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context                 | /api/contexts/Error |
      | @type                    | hydra:Error         |
      | hydra:title              | An error occurred   |
      | hydra:description        | User already active |

  @restContext
  Scenario: Sending a request without activation shall return an explicit error.
    Given database is clean
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/user_activation_requests" with body:
    """
    {}
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context                 | /api/contexts/Error                 |
      | @type                    | hydra:Error                         |
      | hydra:title              | An error occurred                   |
      | hydra:description        | Activation code should not be blank |

  @restContext
  Scenario: Sending a request with empty activation shall return an explicit error.
    Given database is clean
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/user_activation_requests" with body:
    """
    {"activation":""}
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context                 | /api/contexts/Error                 |
      | @type                    | hydra:Error                         |
      | hydra:title              | An error occurred                   |
      | hydra:description        | Activation code should not be blank |
