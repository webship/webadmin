<?php

declare(strict_types=1);

namespace Drupal\webadmin\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\webadmin\SignInRoutes;

/**
 * Hooks for the sign-in screens.
 */
final class SignInHooks {

  public function __construct(
    protected RouteMatchInterface $routeMatch,
  ) {}

  /**
   * Implements hook_page_attachments().
   *
   * The "sign_in_theme" setting picks the theme of the sign-in screens, so a
   * cached sign-in page must go when the setting or the themes change.
   */
  #[Hook('page_attachments')]
  public function pageAttachments(array &$attachments): void {
    if (SignInRoutes::contains($this->routeMatch->getRouteName())) {
      $attachments['#cache']['tags'][] = 'config:webadmin.settings';
      $attachments['#cache']['tags'][] = 'config:system.theme';
    }
  }

}
