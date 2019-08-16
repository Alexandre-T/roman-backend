# features/users-crud.feature
Feature: Users CRUD feature
  # ---------------------------------------------------------------------------------------------------
  # COLLECTION GET
  # ---------------------------------------------------------------------------------------------------
  Scenario: Listing all users without authentication should return that token is not found.
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users"
    Then the response status code should be 401
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON nodes should contain:
      | code                    | 401                              |
      | message                 | JWT Token not found              |

  @restContext
  Scenario: Listing all users with admin authentication should return the 3 users of database.
    Given database is clean
    And I am logged as admin
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context          | /api/contexts/User                  |
      | @id               | /api/users                          |
      | @type             | hydra:Collection                    |
      | hydra:totalItems  | 3                                   |

  @restContext
  Scenario: Listing all users with owner authentication should be refused.
    Given database is clean
    And I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users"
    Then the response status code should be 403
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context                        | /api/contexts/Error |
      | @type                           | hydra:Error         |
      | hydra:title                     | An error occurred   |
      | hydra:description               | Access Denied.      |

  # ---------------------------------------------------------------------------------------------------
  # COLLECTION POST
  # ---------------------------------------------------------------------------------------------------
  @restContext
  Scenario: Create a valid user account should works when not authenticated.
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/users" with body:
    """
     {
        "email": "valid@example.org",
        "nickname": "Valid user",
        "plainPassword": "Valid user"
     }
    """
    Then the response status code should be 201
    And the JSON node "books" should have 0 element
    And the JSON node "id" should not exist
    And the JSON node "uuid" should match "/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89AB][0-9a-f]{3}-[0-9a-f]{12}$/i"
    And the JSON node "@id" should match "%^/api/users/[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89AB][0-9a-f]{3}-[0-9a-f]{12}$%i"
    And the JSON node "password" should not exist
    And the JSON node "roles" should have 1 element
    And the JSON nodes should contain:
      | @context          | /api/contexts/User          |
      | @type             | https://schema.org/Person   |
      | email             | valid@example.org           |
      | nickname          | Valid user                  |
      | roles[0]          | ROLE_USER                   |

  @restContext
  Scenario: Create an empty user should failed because required properties are not set.
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/users" with body:
    """
     {}
    """
    Then the response status code should be 400
    And the JSON nodes should contain:
      | @context          | /api/contexts/ConstraintViolationList |
      | @type             | ConstraintViolationList               |
      | hydra:title       | An error occurred                     |
    And the JSON node "hydra:description" should not be null
    And the JSON node "violations" should have 3 elements
    And the JSON node "violations[0].propertyPath" should be equal to "email"
    And the JSON node "violations[0].message" should be equal to "This value should not be blank."
    And the JSON node "violations[1].propertyPath" should be equal to "nickname"
    And the JSON node "violations[1].message" should be equal to "This value should not be blank."
    And the JSON node "violations[2].propertyPath" should be equal to "plainPassword"
    And the JSON node "violations[2].message" should be equal to "This value should not be blank."

  @restContext
  Scenario: Create a user with too long or too short data admin account should failed because properties are too long.
    Given database is clean
    And I am logged as admin
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/users" with body:
    """
     {
      "email": "---------1---------2---------3---------4---------5---------6---------7---------8---------9---------0---------1---------2---------3---------4---------5---------6---------7---------8---------9---------0---------1---------2---------3---------4---------5---------6---------7---------8---------9---------@example.org",
      "nickname": "---------1---------2---------3---------4---------5---------6---------7---------8---------9---------0---------1---------2---------3---------4---------5---------6---------7---------8---------9---------0---------1---------2---------3---------4---------5---------6---------7---------8---------9---------",
      "plainPassword": "123456"
     }
    """
    Then the response status code should be 400
    And the JSON nodes should contain:
      | @context          | /api/contexts/ConstraintViolationList |
      | @type             | ConstraintViolationList               |
      | hydra:title       | An error occurred                     |
    And the JSON node "hydra:description" should not be null
    And the JSON node "violations" should have 3 elements
    And the JSON node "violations[0].propertyPath" should be equal to "email"
    And the JSON node "violations[0].message" should be equal to "This value is too long. It should have 180 characters or less."
    And the JSON node "violations[1].propertyPath" should be equal to "nickname"
    And the JSON node "violations[1].message" should be equal to "This value is too long. It should have 255 characters or less."
    And the JSON node "violations[2].propertyPath" should be equal to "plainPassword"
    And the JSON node "violations[2].message" should be equal to "This value is too short. It should have 8 characters or more."

  @restContext
  Scenario: Create a valid user with an admin account should works.
    Given database is clean
    And I am logged as admin
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/users" with body:
    """
     {
        "email": "valid@example.org",
        "nickname": "Valid user",
        "plainPassword": "Valid user"
     }
    """
    Then the response status code should be 201
    And the JSON node "books" should have 0 element
    And the JSON node "id" should not exist
    And the JSON node "uuid" should match "/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89AB][0-9a-f]{3}-[0-9a-f]{12}$/i"
    And the JSON node "@id" should match "%^/api/users/[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89AB][0-9a-f]{3}-[0-9a-f]{12}$%i"
    And the JSON node "password" should not exist
    And the JSON node "roles" should have 1 element
    And the JSON nodes should contain:
      | @context          | /api/contexts/User          |
      | @type             | https://schema.org/Person   |
      | email             | valid@example.org           |
      | nickname          | Valid user                  |
      | roles[0]          | ROLE_USER                   |

  @restContext
  Scenario: Create a valid user with an user account should be refused.
    Given database is clean
    And I am logged as user
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/users" with body:
    """
     {
        "email": "valid@example.org",
        "nickname": "Valid user",
        "plainPassword": "Valid user"
     }
    """
    Then the response status code should be 403
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context                        | /api/contexts/Error |
      | @type                           | hydra:Error         |
      | hydra:title                     | An error occurred   |
      | hydra:description               | Access Denied.      |

  # ---------------------------------------------------------------------------------------------------
  # ITEM GET
  # ---------------------------------------------------------------------------------------------------
  @restContext
  Scenario: Show another user with admin authentication
    Given database is clean
    Given I am logged as admin
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/aaaaaaaa-0000-0002-bbbbbbbbbbbbbbbbb"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "id" should not exist
    And the JSON node "password" should not exist
    And the JSON node "roles" should have 1 element
    And the JSON nodes should contain:
      | @context          | /api/contexts/User                                 |
      | @id               | /api/users/aaaaaaaa-0000-0002-bbbbbbbbbbbbbbbbb    |
      | @type             | https://schema.org/Person                          |
      | nickname          | Owner                                              |
      | email             | owner@example.org                                  |
      | roles[0]          | ROLE_USER                                          |

  @restContext
  Scenario: Show himself profile with owner authentication
    Given database is clean
    Given I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/aaaaaaaa-0000-0002-bbbbbbbbbbbbbbbbb"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "id" should not exist
    And the JSON node "password" should not exist
    And the JSON node "roles" should have 1 element
    And the JSON nodes should contain:
      | @context          | /api/contexts/User                                 |
      | @id               | /api/users/aaaaaaaa-0000-0002-bbbbbbbbbbbbbbbbb    |
      | @type             | https://schema.org/Person                          |
      | nickname          | Owner                                              |
      | email             | owner@example.org                                  |
      | roles[0]          | ROLE_USER                                          |

  @restContext
  Scenario: Show another profile with owner authentication will return a 403 error
    Given database is clean
    Given I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/aaaaaaaa-0000-0001-bbbbbbbbbbbbbbbbb"
    Then the response status code should be 403
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context                        | /api/contexts/Error |
      | @type                           | hydra:Error         |
      | hydra:title                     | An error occurred   |
      | hydra:description               | Access Denied.      |

  @restContext
  Scenario: Show a profile without authentication
    Given database is clean
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/aaaaaaaa-0000-0001-bbbbbbbbbbbbbbbbb"
    Then the response status code should be 401
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON nodes should contain:
      | code                    | 401                              |
      | message                 | JWT Token not found              |

  # ---------------------------------------------------------------------------------------------------
  # ITEM PUT
  # ---------------------------------------------------------------------------------------------------
  @restContext
  Scenario: Edit another user with admin authentication shall work.
    Given database is clean
    Given I am logged as admin
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "PUT" request to "/api/users/aaaaaaaa-0000-0002-bbbbbbbbbbbbbbbbb" with body:
    """
     {
        "email": "valid@example.org",
        "nickname": "Valid user",
        "plainPassword": "Valid user"
     }
    """
    Then the response status code should be 200
    And the JSON node "books" should have 2 elements
    And the JSON node "id" should not exist
    And the JSON node "password" should not exist
    And the JSON node "roles" should have 1 element
    And the JSON nodes should contain:
      | @context          | /api/contexts/User          |
      | @type             | https://schema.org/Person   |
      | email             | valid@example.org           |
      | nickname          | Valid user                  |
      | roles[0]          | ROLE_USER                   |

  @restContext
  Scenario: Edit himself with owner authentication shall be authorize.
    Given database is clean
    Given I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "PUT" request to "/api/users/aaaaaaaa-0000-0002-bbbbbbbbbbbbbbbbb" with body:
    """
     {
        "email": "valid@example.org",
        "nickname": "Valid user",
        "plainPassword": "Valid user"
     }
    """
    Then the response status code should be 200
    And the JSON node "books" should have 2 elements
    And the JSON node "id" should not exist
    And the JSON node "password" should not exist
    And the JSON node "roles" should have 1 element
    And the JSON nodes should contain:
      | @context          | /api/contexts/User          |
      | @type             | https://schema.org/Person   |
      | email             | valid@example.org           |
      | nickname          | Valid user                  |
      | roles[0]          | ROLE_USER                   |

  @restContext
  Scenario: Edit another user with owner authentication shall be unavailable
    Given database is clean
    Given I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "PUT" request to "/api/users/aaaaaaaa-0000-0001-bbbbbbbbbbbbbbbbb" with body:
    """
     {
        "email": "valid@example.org",
        "nickname": "Valid user",
        "plainPassword": "Valid user"
     }
    """
    Then the response status code should be 403
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context                        | /api/contexts/Error |
      | @type                           | hydra:Error         |
      | hydra:title                     | An error occurred   |
      | hydra:description               | Access Denied.      |

  @restContext
  Scenario: Edit another user without authentication shall be unavailable
    Given database is clean
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "PUT" request to "/api/users/aaaaaaaa-0000-0001-bbbbbbbbbbbbbbbbb" with body:
    """
     {
        "email": "valid@example.org",
        "nickname": "Valid user",
        "plainPassword": "Valid user"
     }
    """
    Then the response status code should be 401
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON nodes should contain:
      | code                    | 401                              |
      | message                 | JWT Token not found              |

  # ---------------------------------------------------------------------------------------------------
  # ITEM DELETE
  # ---------------------------------------------------------------------------------------------------
  @restContext
  Scenario: Delete another profile with admin authentication shall be available
    Given database is clean
    Given I am logged as admin
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "DELETE" request to "/api/users/aaaaaaaa-0000-0002-bbbbbbbbbbbbbbbbb"
    Then the response status code should be 204
    And the response should be empty

  @restContext
  Scenario: Delete himself with owner authentication shall be available and books shall be deleted
    Given database is clean
    And I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "DELETE" request to "/api/users/aaaaaaaa-0000-0002-bbbbbbbbbbbbbbbbb"
    Then the response status code should be 204
    And the response should be empty
    Given I am logged as admin
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/books"
    Then the response status code should be 200
    And the JSON node "hydra:totalItems" should be equal to 1

  @restContext
  Scenario: Delete another user with owner authentication shall be unavailable.
    Given database is clean
    Given I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "DELETE" request to "/api/users/aaaaaaaa-0000-0001-bbbbbbbbbbbbbbbbb"
    Then the response status code should be 403
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context                        | /api/contexts/Error |
      | @type                           | hydra:Error         |
      | hydra:title                     | An error occurred   |
      | hydra:description               | Access Denied.      |

  @restContext
  Scenario: Delete a user without authentication shall be unavailable.
    Given database is clean
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "DELETE" request to "/api/users/aaaaaaaa-0000-0001-bbbbbbbbbbbbbbbbb"
    Then the response status code should be 401
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON nodes should contain:
      | code                    | 401                              |
      | message                 | JWT Token not found              |
