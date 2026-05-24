Feature: Masquerade as another user
  As a site administrator
  I want to switch to another user account and back
  So that I can troubleshoot what a given user sees without their password

  Background:
    Given I am a logged in user with the "Webmaster" user
     And I add testing users

  Scenario: The modules page lists Masquerade as installed
    When I navigate to "/admin/modules"
    Then I should see "Masquerade"

  Scenario: Admin sees a Masquerade link on a user profile
    When I navigate to "/user/2"
    Then I should see "Masquerade"

  Scenario: Anonymous user does not see the Masquerade link on a user profile
    Given I am an anonymous user
    When I navigate to "/user/1"
    Then I should not see "Masquerade as"
