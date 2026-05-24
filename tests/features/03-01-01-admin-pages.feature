Feature: Pre-check important administration pages
  As a site administrator
  I want to confirm the core administration pages load after install
  So that I can rely on them after installing or updating Web Admin

  Background:
    Given I am a logged in user with the "Webmaster" user

  Scenario: The content overview page loads
    When I navigate to "/admin/content"
    Then I should see "Content"

  Scenario: The structure page loads
    When I navigate to "/admin/structure"
    Then I should see "Block layout"
     And I should see "Content types"
     And I should see "Dashboards"
     And I should see "Views"

  Scenario: The people page loads
    When I navigate to "/admin/people"
    Then I should see "Add user"

  Scenario: The configuration page loads
    When I navigate to "/admin/config"
    Then I should see "Configuration"

  Scenario: The status report loads
    When I navigate to "/admin/reports/status"
    Then I should see "Status report"

  Scenario: The appearance page loads
    When I navigate to "/admin/appearance"
    Then I should see "Gin"
     And I should see "Claro"
