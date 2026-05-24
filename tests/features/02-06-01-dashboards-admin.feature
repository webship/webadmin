Feature: Dashboards bundled with Web Admin
  As a site administrator
  I want the Web Admin dashboards to be available
  So that editors and managers land on a useful overview

  Background:
    Given I am a logged in user with the "Webmaster" user

  Scenario: The dashboards collection lists every bundled dashboard
    When I navigate to "/admin/structure/dashboards"
    Then I should see "Default Dashboard"
     And I should see "Editorial Dashboard"
     And I should see "Management Dashboard"
     And I should see "Webmaster Dashboard"

  Scenario: The default dashboard renders for an administrator
    When I navigate to "/dashboard/default_dashboard"
    Then I should not see "Access denied"
     And I should not see "Page not found"

  Scenario: Admin can open the dashboards settings
    When I navigate to "/admin/system/dashboards-settings"
    Then I should see the button "Save configuration"
