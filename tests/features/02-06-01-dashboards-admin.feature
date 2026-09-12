Feature: Dashboards bundled with Web Admin
  As a site administrator
  I want the Web Admin dashboards to be available
  So that editors and managers land on a useful overview

  Background:
    Given I am a logged in user with the "Webmaster" user

  Scenario: The dashboards collection lists every bundled dashboard
    When I navigate to "/admin/structure/dashboard"
    Then I should see "Webmaster"
     And I should see "Editorial"
     And I should see "Management"

  Scenario: The webmaster dashboard renders for an administrator
    When I navigate to "/admin/dashboard/webmaster"
    Then I should not see "Access denied"
     And I should not see "Page not found"
     And I should see "Site status"
