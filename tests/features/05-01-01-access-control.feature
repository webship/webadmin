Feature: Access control for Web Admin pages
  As a site administrator
  I want administration pages to be protected
  So that only privileged users can manage the site

  Scenario: Anonymous user cannot access the content overview
    Given I am an anonymous user
    When I navigate to "/admin/content"
    Then I should see "Access denied"

  Scenario: Anonymous user cannot access the people page
    Given I am an anonymous user
    When I navigate to "/admin/people"
    Then I should see "Access denied"

  Scenario: Anonymous user cannot access the dashboards collection
    Given I am an anonymous user
    When I navigate to "/admin/structure/dashboards"
    Then I should see "Access denied"

  Scenario: Anonymous user cannot access the workflows config
    Given I am an anonymous user
    When I navigate to "/admin/config/workflow/workflows"
    Then I should see "Access denied"

  Scenario: Authenticated user cannot access the modules page
    Given I am a logged in user with the "Authenticated user" user
    When I navigate to "/admin/modules"
    Then I should see "Access denied"
