Feature: Default Admin administration theme
  As a site administrator
  I want Default Admin to be the active administration theme
  So that the back-end uses the administration theme of Drupal core

  Background:
    Given I am a logged in user with the "Webmaster" user

  Scenario: The appearance page lists Default Admin as installed
    When I navigate to "/admin/appearance"
    Then I should see "Default Admin"

  Scenario: Default Admin is the active administration theme on admin pages
    When I navigate to "/admin/content"
    Then the active admin theme should be "default_admin"
