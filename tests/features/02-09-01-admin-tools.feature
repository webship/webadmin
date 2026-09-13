Feature: Administration tools of the Web Admin recipe
  As a site administrator
  I want Coffee, Project Browser and the Claro blocks available
  So that I can find admin pages, browse projects and use Claro as admin theme

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

  Scenario: The Claro blocks are placed for Claro as admin theme
    When I navigate to "/admin/structure/block/list/claro"
    Then I should see "Page title"
     And I should see "Primary admin actions"
     And I should see "Main page content"
