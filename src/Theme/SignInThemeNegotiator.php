<?php

declare(strict_types=1);

namespace Drupal\webadmin\Theme;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

/**
 * Shows the sign-in screens in UIkit Admin when it is the admin theme.
 *
 * UIkit Admin has a page of its own for the log in, password reset and
 * registration screens. Those screens use the default theme of the site, so
 * this negotiator hands them to the administration theme, only when that
 * theme is UIkit Admin.
 */
final class SignInThemeNegotiator implements ThemeNegotiatorInterface {

  /**
   * The administration theme that has sign-in screens.
   */
  public const THEME = 'uikit_admin';

  /**
   * The routes of the sign-in screens.
   */
  public const ROUTES = [
    'user.login',
    'user.pass',
    'user.register',
    'user.reset',
    'user.reset.form',
    'user.reset.login',
  ];

  public function __construct(
    protected ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match): bool {
    return \in_array($route_match->getRouteName(), self::ROUTES, TRUE)
      && $this->configFactory->get('system.theme')->get('admin') === self::THEME;
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match): string {
    return self::THEME;
  }

}
