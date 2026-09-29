<?php

/**
 * @file
 * Post update functions for Web Admin.
 */

/**
 * Keep the sign-in screens in the administration theme on existing sites.
 */
function webadmin_post_update_sign_in_theme(): void {
  $config = \Drupal::configFactory()->getEditable('webadmin.settings');
  if ($config->get('sign_in_theme') === NULL) {
    $config->set('sign_in_theme', 'admin')->save(TRUE);
  }
}
