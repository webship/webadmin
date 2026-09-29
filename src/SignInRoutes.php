<?php

declare(strict_types=1);

namespace Drupal\webadmin;

/**
 * The routes of the sign-in screens.
 *
 * One list for the theme negotiator, which picks the theme of these screens,
 * and the page layout subscriber, which picks their page.
 *
 * @see \Drupal\webadmin\Theme\SignInThemeNegotiator
 * @see \Drupal\webadmin\EventSubscriber\FrontPageLayoutSubscriber
 */
final class SignInRoutes {

  /**
   * The log in, log out, password reset and registration routes.
   */
  public const ROUTES = [
    'user.login',
    'user.logout.confirm',
    'user.pass',
    'user.register',
    'user.reset',
    'user.reset.form',
    'user.reset.login',
  ];

  /**
   * Tells whether a route is one of the sign-in screens.
   *
   * @param string|null $route_name
   *   The name of the route.
   *
   * @return bool
   *   TRUE for a sign-in screen.
   */
  public static function contains(?string $route_name): bool {
    return \in_array($route_name, self::ROUTES, TRUE);
  }

}
