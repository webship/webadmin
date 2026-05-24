Feature: Content Moderation and Workflows
  As a site administrator
  I want the workflow administration pages available
  So that I can configure editorial moderation states and transitions

  Background:
    Given I am a logged in user with the "Webmaster" user

  Scenario: Admin can open the workflows administration page
    When I navigate to "/admin/config/workflow/workflows"
    Then I should see "Workflows"
     And I should see "Add workflow"

  Scenario: Admin can start adding a content moderation workflow
    When I navigate to "/admin/config/workflow/workflows/add"
    Then I should see a "Label" field
     And I should see the button "Save"
