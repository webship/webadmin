Feature: Gin Login styled sign-in page
  As a site visitor
  I want the login page to use the Gin Login styling
  So that the sign-in experience matches the Web Admin back-end

  Scenario: The login page renders for an anonymous user
    Given I am an anonymous user
    When I navigate to "/user/login"
    Then I should see "Log in"
     And I should see a "Username" field
     And I should see a "Password" field

  Scenario: Admin can open the Gin Login configuration
    Given I am a logged in user with the "Webmaster" user
    When I navigate to "/admin/config/system/configuration/gin-login"
    Then I should not see "Access denied"
     And I should not see "Page not found"
     And I should see the button "Save configuration"
