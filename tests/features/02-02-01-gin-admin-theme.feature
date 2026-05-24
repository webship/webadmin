Feature: Gin administration theme
  As a site administrator
  I want Gin to be the active administration theme
  So that the back-end uses the polished Web Admin experience

  Background:
    Given I am a logged in user with the "Webmaster" user

  Scenario: The appearance page lists Gin as installed
    When I navigate to "/admin/appearance"
    Then I should see "Gin"

  Scenario: Gin is the active administration theme on admin pages
    When I navigate to "/admin/content"
    Then the active admin theme should be "gin"

  Scenario: Admin can open the Gin theme settings
    When I navigate to "/admin/appearance/settings/gin"
    Then I should see "Gin"
     And I should see the button "Save configuration"
