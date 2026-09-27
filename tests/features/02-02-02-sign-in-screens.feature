Feature: Sign-in screens in UIkit Admin
  As a person who works on the site
  I want the sign-in screens in the administration theme
  So that I know I am entering the back office

  Scenario: The log in screen uses the UIkit Admin sign-in page
    Given I am an anonymous user
    When I navigate to "/user/login"
    Then I should see "Log in"
     And the active admin theme should be "uikit_admin"

  Scenario: The password reset screen uses the UIkit Admin sign-in page
    Given I am an anonymous user
    When I navigate to "/user/password"
    Then I should see "Reset your password"
     And the active admin theme should be "uikit_admin"
