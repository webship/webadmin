Feature: Administration tools of the Web Admin recipe
  As a site administrator
  I want Coffee and Project Browser available
  So that I can find admin pages and browse projects

  Background:
    Given I am a logged in user with the "Webmaster" user

  Scenario: The Coffee search returns the administration links
    Then the response status of "/admin/coffee/get-data" should be 200
     And the response body of "/admin/coffee/get-data" should contain "People"

  Scenario: Admin can open the Coffee settings
    When I navigate to "/admin/config/user-interface/coffee"
    Then I should not see "Access denied"
     And I should see the button "Save configuration"

  Scenario: Admin can open the Project Browser
    Then the response status of "/admin/modules/browse/drupalorg_jsonapi" should be 200
     And the response body of "/admin/modules/browse/drupalorg_jsonapi" should contain "Browse projects"
