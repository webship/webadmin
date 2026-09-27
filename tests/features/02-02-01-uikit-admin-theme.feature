Feature: UIkit Admin administration theme
  As a site administrator
  I want UIkit Admin to be the active administration theme
  So that the back-end uses the UIkit administration theme

  Background:
    Given I am a logged in user with the "Webmaster" user

  Scenario: The appearance page lists UIkit Admin as installed
    When I navigate to "/admin/appearance"
    Then I should see "UIkit Admin"

  Scenario: UIkit Admin is the active administration theme on admin pages
    When I navigate to "/admin/content"
    Then the active admin theme should be "uikit_admin"
