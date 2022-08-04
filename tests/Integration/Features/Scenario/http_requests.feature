Feature: Tests HTTP requests responses
  Test the different cases and responses

  Scenario: Run the docker container and make a request
    When I get the content of "/en/doc/AdminDashboard?version=1.7.8.0"
    Then I should get a success response
    When I get the content of "/en/AdminDashboard?version=1.7.8.0"
    Then I should get a success response
