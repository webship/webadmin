Feature: Administration toolbar and shortcuts
  As a site administrator
  I want the toolbar and shortcut tools available
  So that I can move around the back-end efficiently

  Background:
    Given I am a logged in user with the "Webmaster" user

  Scenario: Admin can open the shortcuts administration page
    When I navigate to "/admin/config/user-interface/shortcut"
    Then I should see "Shortcuts"

  Scenario: The Navigation module is not installed
    Then the response status of "/admin/config/user-interface/navigation/settings" should be 404

  Scenario: The administration menu is reachable
    When I navigate to "/admin"
    Then I should see "Administration"
