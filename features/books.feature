# features/books.feature
Feature: Books feature
  Scenario: Listing all books without authentication
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "GET" request to "/api/books/"
    Then the response status code should be 401
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON nodes should contain:
      | message                 | JWT Token not found              |
  Scenario: Listing all books with admin authentication
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "POST" request to "/authentication_token" with body:
    """
    {
      "email": "admin@example.org",
      "password": "admin"
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "token" should exist
