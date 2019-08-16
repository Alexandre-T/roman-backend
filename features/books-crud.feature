# features/books-crud.feature
Feature: Books CRUD feature
  # ---------------------------------------------------------------------------------------------------
  # COLLECTION GET
  # ---------------------------------------------------------------------------------------------------
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
  Scenario: Listing all books with admin authentication should return the 3 books of database.
    Given database is clean
    And I am logged as admin
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
  Scenario: Listing all books with owner authentication should return 2 books owned.
    Given database is clean
    And I am logged as owner
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
  Scenario: Listing all books with user authentication should return 0 book because he owns no one.
    Given database is clean
    And I am logged as user
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

  # ---------------------------------------------------------------------------------------------------
  # COLLECTION POST
  # ---------------------------------------------------------------------------------------------------
  @restContext
  Scenario: Create a book without authentication will failed.
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/books" with body:
    """
     {}
    """
    Then the response status code should be 401
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON nodes should be equal to:
      | code     | 401                 |
      | message  | JWT Token not found |

  @restContext
  Scenario: Create an empty book with admin account should failed because required properties are not set.
    Given database is clean
    And I am logged as admin
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/books" with body:
    """
     {}
    """
    Then the response status code should be 400
    And the JSON nodes should contain:
      | @context          | /api/contexts/ConstraintViolationList |
      | @type             | ConstraintViolationList               |
      | hydra:title       | An error occurred                      |
    And the JSON node "hydra:description" should not be null
    And the JSON node "violations" should have 2 elements

  @restContext
  Scenario: Create a book with too long data admin account should failed because properties are too long.
    Given database is clean
    And I am logged as admin
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/books" with body:
    """
     {
      "owner": "/api/users/aaaaaaaa-0000-0002-bbbbbbbbbbbbbbbbb",
      "title": "---------1---------2---------3---------4---------5---------6---------7---------8---------9---------....0---------1---------2---------3---------4---------5---------6---------7---------8---------9---------0---------1---------2---------3---------4---------5---------6---------7---------8---------9---------",
      "author": "---------1---------2---------3---------4---------5---------6---------7---------8---------9---------0---------1---------2---------3---------4---------5---------6---------7---------8---------9---------0---------1---------2---------3---------4---------5---------6---------7---------8---------9---------"
     }
    """
    Then the response status code should be 400
    And the JSON nodes should contain:
      | @context          | /api/contexts/ConstraintViolationList |
      | @type             | ConstraintViolationList               |
      | hydra:title       | An error occurred                     |
    And the JSON node "hydra:description" should not be null
    And the JSON node "violations" should have 2 elements
    And the JSON node "violations[0].propertyPath" should be equal to "author"
    And the JSON node "violations[0].message" should be equal to "This value is too long. It should have 255 characters or less."
    And the JSON node "violations[1].propertyPath" should be equal to "title"
    And the JSON node "violations[1].message" should be equal to "This value is too long. It should have 255 characters or less."

  @restContext
  Scenario: Create a valid book for owner with admin account should works.
    Given database is clean
    Given I am logged as admin
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/books" with body:
    """
     {
        "title": "A new book manually created",
        "owner": "/api/users/aaaaaaaa-0000-0002-bbbbbbbbbbbbbbbbb",
        "author": "Sample"
     }
    """
    Then the response status code should be 201
    And the JSON node "biography" should be null
    And the JSON node "dramaPitch" should be null
    And the JSON node "taglinePitch" should be null
    And the JSON node "trajectorialPitch" should be null
    And the JSON nodes should contain:
      | @context          | /api/contexts/Book          |
      | @type             | https://schema.org/Book     |
      | author            | Sample                      |
      | title             | A new book manually created |
    And the JSON node "owner.@id" should be equal to "/api/users/aaaaaaaa-0000-0002-bbbbbbbbbbbbbbbbb"
    And the JSON node "owner.@type" should be equal to "https://schema.org/Person"
    And the JSON node "owner.nickname" should be equal to "Owner"
    And the JSON node "owner.uuid" should be equal to "aaaaaaaa-0000-0002-bbbbbbbbbbbbbbbbb"
    And the JSON node "owner.password" should not exist

  @restContext
  Scenario: Create a book for admin with owner account should failed.
    Given database is clean
    Given I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/books" with body:
    """
     {
        "title": "A new book manually created by owner",
        "owner": "/api/users/aaaaaaaa-0000-0001-bbbbbbbbbbbbbbbbb",
        "author": "Impossible"
     }
    """
    Then the response status code should be 403
    And the JSON nodes should contain:
      | @context                        | /api/contexts/Error |
      | @type                           | hydra:Error         |
      | hydra:title                     | An error occurred   |
      | hydra:description               | Access Denied.      |

  @restContext
  Scenario: Create a book without account.
    Given database is clean
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/books" with body:
    """
     {
        "title": "A new book manually created by owner",
        "owner": "/api/users/aaaaaaaa-0000-0001-bbbbbbbbbbbbbbbbb",
        "author": "Impossible"
     }
    """
    Then the response status code should be 401
    And the JSON nodes should be equal to:
      | code     | 401                 |
      | message  | JWT Token not found |

  # ---------------------------------------------------------------------------------------------------
  # ITEM GET
  # ---------------------------------------------------------------------------------------------------
  @restContext
  Scenario: Show non-owned book with admin authentication
    Given database is clean
    Given I am logged as admin
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/books/aaaaaaaa-b00c-0002-bbbbbbbbbbbbbbbbb"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "dramaPitch" should be null
    And the JSON node "taglinePitch" should be null
    And the JSON node "trajectorialPitch" should be null
    And the JSON node "id" should not exist
    And the JSON node "owner.@id" should be equal to "/api/users/aaaaaaaa-0000-0002-bbbbbbbbbbbbbbbbb"
    And the JSON node "owner.@type" should be equal to "https://schema.org/Person"
    And the JSON node "owner.nickname" should be equal to "Owner"
    And the JSON node "owner.uuid" should be equal to "aaaaaaaa-0000-0002-bbbbbbbbbbbbbbbbb"
    And the JSON nodes should contain:
      | @context          | /api/contexts/Book                                 |
      | @id               | /api/books/aaaaaaaa-b00c-0002-bbbbbbbbbbbbbbbbb    |
      | @type             | https://schema.org/Book                            |
      | author            | Owner                                              |
      | title             | First book of owner                                |
      | uuid              | aaaaaaaa-b00c-0002-bbbbbbbbbbbbbbbbb               |

  @restContext
  Scenario: Show owned book with owner authentication
    Given database is clean
    Given I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/books/aaaaaaaa-b00c-0002-bbbbbbbbbbbbbbbbb"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "dramaPitch" should be null
    And the JSON node "taglinePitch" should be null
    And the JSON node "trajectorialPitch" should be null
    And the JSON node "id" should not exist
    And the JSON node "owner.@id" should be equal to "/api/users/aaaaaaaa-0000-0002-bbbbbbbbbbbbbbbbb"
    And the JSON node "owner.@type" should be equal to "https://schema.org/Person"
    And the JSON node "owner.nickname" should be equal to "Owner"
    And the JSON node "owner.uuid" should be equal to "aaaaaaaa-0000-0002-bbbbbbbbbbbbbbbbb"
    And the JSON nodes should contain:
      | @context          | /api/contexts/Book                                 |
      | @id               | /api/books/aaaaaaaa-b00c-0002-bbbbbbbbbbbbbbbbb    |
      | @type             | https://schema.org/Book                            |
      | author            | Owner                                              |
      | title             | First book of owner                                |
      | uuid              | aaaaaaaa-b00c-0002-bbbbbbbbbbbbbbbbb               |

  @restContext
  Scenario: Show non-owned book with owner authentication
    Given database is clean
    Given I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/books/aaaaaaaa-b00c-0001-bbbbbbbbbbbbbbbbb"
    Then the response status code should be 404
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context          | /api/contexts/Error |
      | @type             | hydra:Error         |
      | hydra:title       | An error occurred   |
      | hydra:description | Not Found           |

  @restContext
  Scenario: Show non-owned book with user authentication
    Given database is clean
    Given I am logged as user
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/books/aaaaaaaa-b00c-0001-bbbbbbbbbbbbbbbbb"
    Then the response status code should be 404
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context          | /api/contexts/Error |
      | @type             | hydra:Error         |
      | hydra:title       | An error occurred   |
      | hydra:description | Not Found           |

  # ---------------------------------------------------------------------------------------------------
  # ITEM PUT
  # ---------------------------------------------------------------------------------------------------
  @restContext
  Scenario: Edit a non-owned book with admin authentication
    Given database is clean
    Given I am logged as admin
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "PUT" request to "/api/books/aaaaaaaa-b00c-0002-bbbbbbbbbbbbbbbbb" with body:
    """
    {
       "title": "New title",
       "owner": "/api/users/aaaaaaaa-0000-0003-bbbbbbbbbbbbbbbbb",
       "dramaPitch": "Drama pitch"
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "taglinePitch" should be null
    And the JSON node "trajectorialPitch" should be null
    And the JSON node "id" should not exist
    And the JSON node "owner.@id" should be equal to "/api/users/aaaaaaaa-0000-0003-bbbbbbbbbbbbbbbbb"
    And the JSON node "owner.@type" should be equal to "https://schema.org/Person"
    And the JSON node "owner.nickname" should be equal to "User"
    And the JSON node "owner.uuid" should be equal to "aaaaaaaa-0000-0003-bbbbbbbbbbbbbbbbb"
    And the JSON nodes should contain:
      | @context          | /api/contexts/Book                                 |
      | @id               | /api/books/aaaaaaaa-b00c-0002-bbbbbbbbbbbbbbbbb    |
      | @type             | https://schema.org/Book                            |
      | author            | Owner                                              |
      | title             | New title                                          |
      | uuid              | aaaaaaaa-b00c-0002-bbbbbbbbbbbbbbbbb               |
      | dramaPitch        | Drama pitch                                        |

  @restContext
  Scenario: Edit owned book with owner authentication without forwarding owner
    Given database is clean
    Given I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "PUT" request to "/api/books/aaaaaaaa-b00c-0002-bbbbbbbbbbbbbbbbb" with body:
    """
    {
       "title": "New title",
       "owner": "/api/users/aaaaaaaa-0000-0003-bbbbbbbbbbbbbbbbb",
       "dramePitch": "Drama pitch"
    }
    """
    Then the response status code should be 403
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"

  @restContext
  Scenario: Edit non-owned book with owner authentication
    Given database is clean
    Given I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "PUT" request to "/api/books/aaaaaaaa-b00c-0001-bbbbbbbbbbbbbbbbb" with body:
    """
    {
       "title": "New title",
       "dramePitch": "Drama pitch"
    }
    """
    Then the response status code should be 404
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context          | /api/contexts/Error |
      | @type             | hydra:Error         |
      | hydra:title       | An error occurred   |
      | hydra:description | Not Found           |

  @restContext
  Scenario: Edit owned book with owner authentication and changing owner
    Given database is clean
    Given I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "PUT" request to "/api/books/aaaaaaaa-b00c-0001-bbbbbbbbbbbbbbbbb" with body:
    """
    {
      title: "Forwarded",
      owner: "/api/books/aaaaaaaa-0000-0003-bbbbbbbbbbbbbbbbb"
    }
    """
    Then the response status code should be 404
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context          | /api/contexts/Error |
      | @type             | hydra:Error         |
      | hydra:title       | An error occurred   |
      | hydra:description | Not Found           |

  # ---------------------------------------------------------------------------------------------------
  # ITEM DELETE
  # ---------------------------------------------------------------------------------------------------
  @restContext
  Scenario: Delete a non-owned book with admin authentication
    Given database is clean
    Given I am logged as admin
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "DELETE" request to "/api/books/aaaaaaaa-b00c-0002-bbbbbbbbbbbbbbbbb"
    Then the response status code should be 204
    And the response should be empty

  @restContext
  Scenario: Delete a owned book with owner authentication
    Given database is clean
    Given I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "DELETE" request to "/api/books/aaaaaaaa-b00c-0002-bbbbbbbbbbbbbbbbb"
    Then the response status code should be 204
    And the response should be empty

  @restContext
  Scenario: Delete a non-owned book with owner authentication
    Given database is clean
    Given I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "DELETE" request to "/api/books/aaaaaaaa-b00c-0001-bbbbbbbbbbbbbbbbb"
    Then the response status code should be 404
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context          | /api/contexts/Error |
      | @type             | hydra:Error         |
      | hydra:title       | An error occurred   |
      | hydra:description | Not Found           |

  @restContext
  Scenario: Delete a non-existant book with owner authentication
    Given database is clean
    Given I am logged as owner
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "DELETE" request to "/api/books/aaaaaaaa-b00c-0004-bbbbbbbbbbbbbbbbb"
    Then the response status code should be 404
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should contain:
      | @context          | /api/contexts/Error |
      | @type             | hydra:Error         |
      | hydra:title       | An error occurred   |
      | hydra:description | Not Found           |