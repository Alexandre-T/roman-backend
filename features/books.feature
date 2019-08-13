# features/books.feature
Feature: Books feature
  Scenario: Listing all books without authentication
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "GET" request to "/api/books"
    Then the response status code should be 401
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON nodes should contain:
      | message                 | JWT Token not found              |
  @loginAsAdmin @logout
  Scenario: Listing all books with admin authentication
    Given I am login as admin
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
#    And I add "Authorization" header equal to "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NjU2ODcwMjUsImV4cCI6MTU2NTY5MDYyNSwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6ImFkbWluQGV4YW1wbGUub3JnIn0.VuR9p1skAQiHptT2YBdAdRLTH5cTFBvT0DFRG-EdAyZ9gTI62KMc_B4dyzJV9cMt2VYh4vORMqUGyc13u5HqtAcfbnNS7pEv4EVc7xECpMP4SMDq75169XBVGsGPAdzzbmizkK8szQQi_Lp-KcI4dVAo8WY-Fe3zQqjW6VOm-F0JiYI9xrp8pEbemuyKseWSJGC2XvZsaYGiM6b7vVFVoDKtzB0CkxCqWLP6f3urK08XwVrA1MNZEFqn2JK6ODNJWE1GlAX92UtiLPjZv6L43sdHhv2fjhdSRwUMZ41C5-4yzostG_wFW6y4Fnu8AfzQPJnIaVgQRH6AYa7CEErW-dFFYLAbn0NgRBqrCFXZSdRuKjnnPbuES7Y16M7Vc0eVb2rnpubcl8nNRRdXqpBtOhiIMUeRVdVPPgAL6ilU3mh2YbtauFCMIAL0m-2keN4DSTqDsUi1HsBPxqwY9XDe8lUVkFGvTfFFwQ1deAfzEkg_0HNRhGzWEoEKbyBgRnnUJOmT9peOgRGmhRItKIWOoY0QB0ZoTDNH7nekNfkoItSLKy9NQ4Z6rAwCOCosPwevBIGIBZnWcOfJFp1dYl0yebSrzYbtWQsGTjOmJd_hyFy0jouNhjCyomLK4MpxkScRg5C7zx1MI6TPFJ5SzTlqwVmWVDfoqLuXOtAr9zMAqYA"
    And I send a "GET" request to "/api/books"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json; charset=utf-8"
