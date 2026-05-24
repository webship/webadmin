Feature: Views Bulk Operations on the People admin view
  As a content administrator
  I want bulk actions on the People list
  So that I can act on many users at once without opening each profile

  Background:
    Given I am a logged in user with the "Webmaster" user

  Scenario: The People list exposes a bulk action selector and submit
    When I navigate to "/admin/people"
    Then I should see "Apply to selected"

  Scenario: The People bulk form offers role and status actions
    When I navigate to "/admin/people"
    Then I should see the bulk action "Block the selected user"
     And I should see the bulk action "Add the Administrator role to the selected user"
