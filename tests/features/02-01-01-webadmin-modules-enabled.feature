Feature: Web Admin bundled modules are enabled
  As an admin user
  I want to verify that the Web Admin recipe enables every administration tool
  So that I know the recipe ran cleanly during install

  Background:
    Given I am a logged in user with the "Webmaster" user

  Scenario: Modules report page lists Web Admin and its tools as enabled
    When I navigate to "/admin/modules"
    Then I should see "Web Admin"
     And I should see "Masquerade"
     And I should see "Views Bulk Operations"
     And I should see "Views Bulk Edit"
     And I should see "Dashboards"
     And I should see "Content Moderation"
     And I should see "Workflows"
     And I should see "Navigation"
     And I should see "Gin Toolbar"
     And I should see "Gin Login"
