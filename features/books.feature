# features/books.feature
Feature: Books feature
  Scenario: Listing all books without authentication
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/books"
    Then the response status code should be 401
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON nodes should contain:
      | message                 | JWT Token not found              |
  @restContext
  Scenario: Listing all books with admin authentication should return 3 books.
    Given I am logged as admin
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/books"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context          | /api/contexts/Book                  |
      | @id               | /api/books                          |
      | @type             | hydra:Collection                    |
      | hydra:totalItems  | 3                                   |
  @restContext
  Scenario: Listing all books with owner authentication should return 2 books.
    Given I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/books"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context          | /api/contexts/Book                  |
      | @id               | /api/books                          |
      | @type             | hydra:Collection                    |
      | hydra:totalItems  | 2                                   |
  @restContext
  Scenario: Listing all books with user authentication should return 0 book.
    Given I am logged as user
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/books"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:member" should have 0 element
    And the JSON node "hydra:totalItems" should be equal to "0"
    And the JSON nodes should contain:
      | @context          | /api/contexts/Book                  |
      | @id               | /api/books                          |
      | @type             | hydra:Collection                    |
  @restContext
  Scenario: Show owned book with owner authentication
    Given I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/books/aaaaaaaa-b00c-0002-bbbbbbbbbbbbbbbbb"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    # TODO complete
  @restContext
  Scenario: Show non-owned book with owner authentication
    Given I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/books/aaaaaaaa-b00c-0001-bbbbbbbbbbbbbbbbb"
    Then the response status code should be 404
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    # TODO complete
