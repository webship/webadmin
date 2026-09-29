'use strict';

const { After, Given, Then, When } = require('@cucumber/cucumber');
const { friendly } = require('webship-js/tests/step-definitions/webship');

/**
 * Run a step body and rethrow any failure as a tester-friendly error.
 */
async function attempt(body, message) {
  try {
    await body();
  } catch (err) {
    throw friendly(message, err);
  }
}

/**
 * Log in as a named test user defined in cucumber.js worldParameters.users.
 *
 * The Webmaster row is the site-install super-admin. Every other row is
 * provisioned by `Given I add testing users` (see below).
 *
 * Example #1: Given I am a logged in user with the "Webmaster" user
 * Example #2: Given I am a logged in user with the "Content editor" user
 * Example #3: Given I am a logged in user with the "Authenticated user" user
 */
Given(/^I am a logged in user with( the)*( username)* "([^"]*)?"( user)?$/, async function (theCase, usernameCase, key, userCase) {
  const users = this.parameters.users || {};
  if (!(key in users)) {
    throw new Error(`No user named "${key}" in cucumber.js worldParameters.users`);
  }
  const { username, password } = users[key];
  if (!username || !password) {
    throw new Error(`User "${key}" is missing username or password in worldParameters.users`);
  }
  await this.page.goto(`${this.parameters.launchUrl}/user/login`);
  // View Password adds a "Show password" button labeled after the field, so
  // target the login form inputs by id.
  await this.page.locator('#edit-name').fill(username);
  await this.page.locator('#edit-pass').fill(password);
  await this.page.locator('input[value="Log in"]').click();
  await this.page.waitForLoadState('networkidle');
});

/**
 * Provision every non-admin user from cucumber.js worldParameters.users.
 *
 * Example #1: Given I add testing users
 * Example #2: And I add the testing users
 */
Given(/^(?:I |we )?add( the)? testing users$/, async function (theCase) {
  const users = this.parameters.users || {};
  for (const [key, info] of Object.entries(users)) {
    if (info.isAdmin) continue;
    await this.page.goto(`${this.parameters.launchUrl}/admin/people/create`);
    await this.page.locator('#edit-name').fill(info.username);
    await this.page.locator('#edit-mail').fill(info.email || `${info.username}@example.test`);
    await this.page.locator('#edit-pass-pass1').fill(info.password);
    await this.page.locator('#edit-pass-pass2').fill(info.password);
    for (const role of info.roles || []) {
      const cb = this.page.locator(`input[name="roles[${role}]"]`);
      if (await cb.count() > 0) await cb.check();
    }
    await this.page.locator('#edit-submit').click();
    await this.page.waitForLoadState('networkidle');
  }
});

/**
 * Resolve a form field locator by label.
 */
function fieldLocator(page, label) {
  return page
    .locator('label.form-item__label, label.form-required, label')
    .filter({ hasText: new RegExp(`^\\s*${label.replace(/[.*+?^${}()|[\\]\\\\]/g, '\\$&')}(\\s|$)`, 'i') })
    .first();
}

/**
 * Assert that a form field with the given label is visible on the page.
 *
 * Example #1: Then I should see a "Title" field
 * Example #2: Then I should see a "Description" field
 */
Then(/^(?:I |we )?should see a "([^"]*)" field$/, async function (label) {
  await attempt(async () => {
    const locator = fieldLocator(this.page, label);
    await locator.waitFor({ state: 'visible', timeout: 10000 });
  }, `Expected to find a field labeled "${label}"`);
});

/**
 * Assert that a form field with the given label (with article "an") is visible.
 *
 * Example #1: Then I should see an "Image" field
 */
Then(/^(?:I |we )?should see an "([^"]*)" field$/, async function (label) {
  await attempt(async () => {
    const locator = fieldLocator(this.page, label);
    await locator.waitFor({ state: 'visible', timeout: 10000 });
  }, `Expected to find a field labeled "${label}"`);
});

/**
 * Assert that a button with the given text is visible on the page.
 *
 * Example #1: Then I should see the button "Save"
 */
Then(/^(?:I |we )?should see the button "([^"]*)"$/, async function (text) {
  await attempt(async () => {
    const locator = this.page.getByRole('button', { name: text, exact: false }).first();
    await locator.waitFor({ state: 'visible', timeout: 10000 });
  }, `Expected to find a button with text "${text}"`);
});

/**
 * Assert that the response of a path is the given HTTP status code.
 *
 * Example #1: Then the response status of "/admin/content" should be 200
 * Example #2: Then the response status of "/admin/people/masquerade" should be 200
 */
Then(/^the response status of "([^"]+)" should be (\d+)$/, async function (path, status) {
  await attempt(async () => {
    const url = `${this.parameters.launchUrl}${path}`;
    const response = await this.page.request.get(url, { failOnStatusCode: false });
    const actual = response.status();
    if (String(actual) !== String(status)) {
      throw new Error(`GET ${path} returned ${actual}, expected ${status}`);
    }
  }, `Unexpected HTTP status for "${path}"`);
});

/**
 * Assert that the response body of a path contains a string.
 *
 * Example #1: Then the response body of "/admin/modules" should contain "Web Admin"
 */
Then(/^the response body of "([^"]+)" should contain "([^"]*)"$/, async function (path, needle) {
  await attempt(async () => {
    const url = `${this.parameters.launchUrl}${path}`;
    const response = await this.page.request.get(url, { failOnStatusCode: false });
    const text = await response.text();
    if (!text.includes(needle)) {
      throw new Error(`Response body of ${path} did not contain "${needle}"`);
    }
  }, `Expected response body of "${path}" to contain "${needle}"`);
});

/**
 * Assert that the active admin (back-end) theme is the given machine name.
 *
 * Web Admin sets Default Admin, the administration theme of Drupal core, as the
 * administration theme through its default recipe. A theme ships either in
 * core or in contrib, so both asset paths count as a match.
 *
 * Example #1: Then the active admin theme should be "uikit_admin"
 */
Then(/^the active admin theme should be "([^"]+)"$/, async function (theme) {
  await attempt(async () => {
    const html = await this.page.content();
    const onBody = await this.page.locator(`body[class*="${theme}"], html[data-drupal-admin-theme="${theme}"]`).count();
    if (onBody === 0
      && !html.includes(`/core/themes/${theme}/`)
      && !html.includes(`/themes/contrib/${theme}/`)) {
      throw new Error(`Active admin theme does not appear to be "${theme}"`);
    }
  }, `Expected the active admin theme to be "${theme}"`);
});

/**
 * Assert that a checkbox / row for a named bulk action option exists in a
 * Views Bulk Operations action select.
 *
 * Example #1: Then I should see the bulk action "Delete content"
 */
Then(/^(?:I |we )?should see the bulk action "([^"]+)"$/, async function (label) {
  await attempt(async () => {
    const option = this.page.locator('select[name="action"] option, .vbo-action-configuration, label')
      .filter({ hasText: new RegExp(label.replace(/[.*+?^${}()|[\\]\\\\]/g, '\\$&'), 'i') })
      .first();
    await option.waitFor({ state: 'attached', timeout: 10000 });
  }, `Expected to find a bulk action option labeled "${label}"`);
});

/**
 * Import one simple config value through the single import form, as the
 * Webmaster, then sign out again.
 *
 * @param {object} world
 *   The Cucumber world.
 * @param {string} name
 *   The name of the simple config, like "webadmin.settings".
 * @param {string} yaml
 *   The YAML of the whole config object.
 */
async function importSimpleConfig(world, name, yaml) {
  const { username, password } = (world.parameters.users || {}).Webmaster || {};
  const base = world.parameters.launchUrl;
  await world.context.clearCookies();
  await world.page.goto(`${base}/user/login`);
  await world.page.locator('#edit-name').fill(username);
  await world.page.locator('#edit-pass').fill(password);
  await world.page.locator('input[value="Log in"]').click();
  await world.page.waitForLoadState('networkidle');
  await world.page.goto(
    `${base}/admin/config/development/configuration/single/import`,
  );
  await world.page
    .locator('select[name="config_type"]')
    .selectOption('system.simple');
  await world.page.locator('input[name="config_name"]').fill(name);
  await world.page.locator('textarea[name="import"]').fill(yaml);
  await world.page.locator('input[type="submit"][value="Import"]').click();
  await world.page.waitForLoadState('networkidle');
  // The same value again: core refuses the import, and the value is set.
  const unchanged = await world.page
    .getByText('There are no changes to import.')
    .count();
  if (!unchanged) {
    await world.page.locator('input[type="submit"][value="Confirm"]').click();
    await world.page
      .getByText('The configuration was imported successfully.')
      .waitFor({ timeout: 30000 });
  }
  await world.context.clearCookies();
}

/**
 * Choose the theme that serves the sign-in screens.
 *
 * "admin" hands them to UIkit Admin, when it is the administration theme.
 * "default" leaves them to the default theme of the site. Scenarios tagged
 * @sign-in-theme get "admin" back when they end.
 *
 * Example #1: Given the sign-in screens are served by the "default" theme
 * Example #2: Given the sign-in screens are served by the "admin" theme
 */
Given(
  /^the sign-in screens are served by the "(admin|default)" theme$/,
  async function (theme) {
    await attempt(
      () =>
        importSimpleConfig(
          this,
          'webadmin.settings',
          `sign_in_theme: ${theme}`,
        ),
      `Could not set webadmin.settings:sign_in_theme to "${theme}"`,
    );
  },
);

After({ tags: '@sign-in-theme' }, async function () {
  await importSimpleConfig(this, 'webadmin.settings', 'sign_in_theme: admin');
});

/**
 * Assert that an element with a class that contains a string is (or is not)
 * on the page.
 *
 * Example #1: Then I should see an element with a class containing "uikit-admin-sign-in"
 * Example #2: Then I should not see an element with a class containing "uikit-admin-"
 */
Then(
  /^(?:I |we )?should( not)? see an element with a class containing "([^"]+)"$/,
  async function (not, needle) {
    await attempt(async () => {
      const count = await this.page.locator(`[class*="${needle}"]`).count();
      if (not && count > 0) {
        throw new Error(
          `Found ${count} element(s) with a class containing "${needle}"`,
        );
      }
      if (!not && count === 0) {
        throw new Error(`No element has a class containing "${needle}"`);
      }
    }, `Unexpected elements with a class containing "${needle}"`);
  },
);

/**
 * Assert that the page loads no asset of a theme.
 *
 * Example #1: Then the page should not load the assets of the "uikit_admin" theme
 */
Then(
  /^the page should not load the assets of the "([^"]+)" theme$/,
  async function (theme) {
    await attempt(async () => {
      const html = await this.page.content();
      if (
        html.includes(`/themes/contrib/${theme}/`) ||
        html.includes(`/core/themes/${theme}/`)
      ) {
        throw new Error(`The page loads assets of "${theme}"`);
      }
    }, `Expected the page not to load the "${theme}" theme`);
  },
);
