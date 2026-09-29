<?php

declare(strict_types=1);

namespace Drupal\webadmin\Theme;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;
use Drupal\webadmin\SignInRoutes;

/**
 * Shows the sign-in screens in UIkit Admin when it is the admin theme.
 *
 * UIkit Admin has a page of its own for the log in, log out, password reset
 * and registration screens. Those screens use the default theme of the site,
 * so this negotiator hands them to the administration theme, when that theme
 * is UIkit Admin and the "sign_in_theme" setting of Web Admin is "admin". With
 * "default", the default theme of the site serves them.
 */
final class SignInThemeNegotiator implements ThemeNegotiatorInterface {

  /**
   * The administration theme that has sign-in screens.
   */
  public const THEME = 'uikit_admin';

  public function __construct(
    protected ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match): bool {
    return SignInRoutes::contains($route_match->getRouteName())
      && ($this->configFactory->get('webadmin.settings')->get('sign_in_theme') ?? 'admin') === 'admin'
      && $this->configFactory->get('system.theme')->get('admin') === self::THEME;
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match): string {
    return self::THEME;
  }

}
