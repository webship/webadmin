Feature: Sign-in screens
  As a person who works on the site
  I want to choose the theme of the sign-in screens
  So that people sign in in the administration theme or in the front theme

  Scenario: The log in screen uses the UIkit Admin sign-in page
    Given I am an anonymous user
    When I navigate to "/user/login"
    Then I should see "Log in"
     And the active admin theme should be "uikit_admin"
     And I should see an element with a class containing "uikit-admin-sign-in"

  Scenario: The password reset screen uses the UIkit Admin sign-in page
    Given I am an anonymous user
    When I navigate to "/user/password"
    Then I should see "Reset your password"
     And the active admin theme should be "uikit_admin"

  @sign-in-theme
  Scenario: With the "default" sign-in theme, the front theme serves the log in screen
    Given the sign-in screens are served by the "default" theme
     And I am an anonymous user
    When I navigate to "/user/login"
    Then I should see "Log in"
     And I should not see an element with a class containing "uikit-admin-"
     And the page should not load the assets of the "uikit_admin" theme

  @sign-in-theme
  Scenario: With the "default" sign-in theme, the front theme serves the password reset screen
    Given the sign-in screens are served by the "default" theme
     And I am an anonymous user
    When I navigate to "/user/password"
    Then I should see "Reset your password"
     And I should not see an element with a class containing "uikit-admin-"

  Scenario: With the "admin" sign-in theme, UIkit Admin serves the log in screen again
    Given the sign-in screens are served by the "admin" theme
     And I am an anonymous user
    When I navigate to "/user/login"
    Then I should see an element with a class containing "uikit-admin-sign-in"
