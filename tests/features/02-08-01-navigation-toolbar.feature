Feature: Administration navigation and shortcuts
  As a site administrator
  I want the navigation, toolbar and shortcut tools available
  So that I can move around the back-end efficiently

  Background:
    Given I am a logged in user with the "Webmaster" user

  Scenario: Admin can open the shortcuts administration page
    When I navigate to "/admin/config/user-interface/shortcut"
    Then I should see "Shortcuts"

  Scenario: Navigation settings are reachable
    When I navigate to "/admin/config/user-interface/navigation/settings"
    Then I should not see "Access denied"
     And I should not see "Page not found"

  Scenario: The administration menu is reachable
    When I navigate to "/admin"
    Then I should see "Administration"
