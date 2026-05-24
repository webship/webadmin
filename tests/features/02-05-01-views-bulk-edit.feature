Feature: Views Bulk Edit module is enabled and registered
  As a content administrator
  I want Views Bulk Edit installed by the Web Admin recipe
  So that I can attach a "Modify field values" action to any custom view

  Background:
    Given I am a logged in user with the "Webmaster" user

  Scenario: The modules page lists Views Bulk Edit as installed
    When I navigate to "/admin/modules"
    Then I should see "Views Bulk Edit"

  Scenario: The Views Bulk Edit permission is exposed for role configuration
    When I navigate to "/admin/people/permissions/module/views_bulk_edit"
    Then I should not see "Page not found"
     And I should see "Allow bulk edit of entities"
