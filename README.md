# Web Admin

Website Administration tools.

Maintained by [Webship](https://www.drupal.org/project/webship). Webship and the
[Website Starter](https://www.drupal.org/project/website_starter) use the
[UI Suite UIkit](https://www.drupal.org/project/ui_suite_uikit) theme, with [UIkit](https://getuikit.com) and
[HTMX](https://htmx.org), on top of Drupal and [Display Builder](https://www.drupal.org/project/display_builder).

* [Automatic Updates](https://www.drupal.org/project/automatic_updates): ^4.1
* [Coffee](https://www.drupal.org/project/coffee): ^2
* [Drupical](https://www.drupal.org/project/drupical): ^1
* [Project Browser](https://www.drupal.org/project/project_browser): ^2.1-beta3
* [Simple Add More](https://www.drupal.org/project/sam): ^1.2
* [Tagify](https://www.drupal.org/project/tagify): ^1.2
* [View Password](https://www.drupal.org/project/view_password): ^6
* [Views Bulk Operations](https://www.drupal.org/project/views_bulk_operations): ~4.4.0
* [Views Bulk Edit](https://www.drupal.org/project/views_bulk_edit): ~3.0
* [Masquerade](https://www.drupal.org/project/masquerade): ~2.0
* [UIkit Admin](https://www.drupal.org/project/uikit_admin): ~4.0

The default recipe also installs the core administration tools: Announcements,
Configuration Manager, Contextual Links, Database Logging, Field UI, Help,
Update Manager, Views UI, Content Moderation and Workflows.
It places the [UIkit Admin](https://www.drupal.org/project/uikit_admin)
blocks (breadcrumbs, content, help, local actions, messages, page title, primary
and secondary local tasks), makes UIkit Admin the administration theme, content
editing included, shows the log in, password reset and registration screens in
UIkit Admin (its sign-in page), and grants authenticated users the Coffee, contextual links and
administration theme permissions.

The `sign_in_theme` setting picks the theme of the sign-in screens (log in, log
out, password reset and registration). With `admin`, the default, UIkit Admin
serves them when it is the administration theme. With `default`, the default
theme of the site serves them, with its own sign-in page:

```bash
drush config:set webadmin.settings sign_in_theme default -y
```

On a site with Display Builder page layouts, the layouts of the front theme stay
out of the pages another theme renders: the sign-in screens keep the UIkit Admin
page, not the front page layout. On the sign-in screens, a page layout stays only
when it is written for them: the one the active theme chose in its
`sign_in_page_layout` setting, or one with a "Current theme" condition for the
active theme. Any other page layout gives way to the sign-in page of the theme.

Web Admin does not install Layout Builder, Navigation or a dashboard module. The
dashboards come from the [Web Dashboard](https://www.drupal.org/project/webdash)
recipe, built with Display Builder on the
[Web Dashboard](https://www.drupal.org/project/webdashboard) module.