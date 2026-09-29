<?php

declare(strict_types=1);

namespace Drupal\webadmin\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Render\PageDisplayVariantSelectionEvent;
use Drupal\Core\Render\RenderEvents;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\webadmin\SignInRoutes;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Keeps the page layouts of the front theme out of the other themes.
 *
 * Display Builder page layouts are built for the default theme, and take
 * every page that is not an administration route. The sign-in screens are not
 * administration routes, yet this module shows them in UIkit Admin (see
 * SignInThemeNegotiator): without this subscriber, UIkit Admin would draw the
 * page layout of the front theme, and lose its own sign-in page. A page
 * rendered by a theme the page layout is not built for (the theme of its
 * components, its theme dependency, or else the default theme) gets the page
 * of its own theme and its block layout. A page layout with a "current_theme"
 * condition for the active theme always stays.
 *
 * The sign-in screens keep a page layout only when it is written for them:
 * the page layout the active theme chose for its sign-in screens (the
 * "sign_in_page_layout" theme setting), or one with a "current_theme"
 * condition for the active theme. Any other page layout, like a default one
 * with no condition, gives way to the sign-in page of the theme.
 *
 * @see \Drupal\webadmin\Theme\SignInThemeNegotiator
 * @see \Drupal\display_builder_page_layout\EventSubscriber\PageVariantSubscriber
 */
final class FrontPageLayoutSubscriber implements EventSubscriberInterface {

  /**
   * The page variant of Display Builder page layouts.
   */
  private const PAGE_LAYOUT_VARIANT = 'display_builder_page_layout';

  public function __construct(
    protected ThemeManagerInterface $themeManager,
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected ThemeHandlerInterface $themeHandler,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    // After the page layout subscriber, at -100, and before Display Builder's
    // own subscriber for its builder and preview pages, at -200.
    return [
      RenderEvents::SELECT_PAGE_DISPLAY_VARIANT => [
        ['onSelectPageDisplayVariant', -150],
      ],
    ];
  }

  /**
   * Selects the block page when a page layout is not built for this page.
   *
   * @param \Drupal\Core\Render\PageDisplayVariantSelectionEvent $event
   *   The event to process.
   */
  public function onSelectPageDisplayVariant(PageDisplayVariantSelectionEvent $event): void {
    if ($event->getPluginId() !== self::PAGE_LAYOUT_VARIANT) {
      return;
    }
    $theme = $this->themeManager->getActiveTheme()->getName();
    $event->addCacheContexts(['theme']);
    $event->addCacheTags(['config:webadmin.settings', 'config:' . $theme . '.settings']);
    $page_layout = $this->currentPageLayout();

    if (SignInRoutes::contains($event->getRouteMatch()->getRouteName())) {
      $keep = $page_layout
        && ($page_layout->id() === $this->signInPageLayout($theme) || $this->hasThemeCondition($page_layout, $theme));
    }
    else {
      $keep = \in_array($theme, $this->layoutThemes($page_layout), TRUE)
        || ($page_layout && $this->hasThemeCondition($page_layout, $theme));
    }

    if (!$keep) {
      $event->setPluginId('block_page');
    }
  }

  /**
   * The page layout that matches the current page.
   *
   * @return \Drupal\Core\Config\Entity\ConfigEntityInterface|null
   *   The page layout, or NULL when no page layout matches.
   */
  private function currentPageLayout(): ?ConfigEntityInterface {
    $access_control = $this->entityTypeManager->getAccessControlHandler('page_layout');
    $page_layout = \method_exists($access_control, 'loadCurrentPageLayout') ? $access_control->loadCurrentPageLayout() : NULL;
    return $page_layout instanceof ConfigEntityInterface ? $page_layout : NULL;
  }

  /**
   * The page layout a theme chose for its sign-in screens.
   *
   * The themes add the "sign_in_page_layout" setting in their own time, so a
   * theme without it has none.
   *
   * @param string $theme
   *   The machine name of the theme.
   *
   * @return string|null
   *   The ID of the page layout, or NULL.
   */
  private function signInPageLayout(string $theme): ?string {
    $id = $this->configFactory->get($theme . '.settings')->get('sign_in_page_layout');
    return \is_string($id) && $id !== '' ? $id : NULL;
  }

  /**
   * Tells whether a page layout has a "current_theme" condition for a theme.
   *
   * @param \Drupal\Core\Config\Entity\ConfigEntityInterface $page_layout
   *   The page layout.
   * @param string $theme
   *   The machine name of the theme.
   *
   * @return bool
   *   TRUE when the page layout is shown only in this theme.
   */
  private function hasThemeCondition(ConfigEntityInterface $page_layout, string $theme): bool {
    $condition = $page_layout->get('conditions')['current_theme'] ?? NULL;
    return \is_array($condition)
      && ($condition['theme'] ?? NULL) === $theme
      && empty($condition['negate']);
  }

  /**
   * The themes the page layout of the current page is built for.
   *
   * A page layout is built with the components of a theme (like
   * "ui_suite_uikit:navbar"), and can depend on that theme. One without either
   * is built for the default theme.
   *
   * @param \Drupal\Core\Config\Entity\ConfigEntityInterface|null $page_layout
   *   The page layout of the current page, if any.
   *
   * @return string[]
   *   The machine names of the themes.
   */
  private function layoutThemes(?ConfigEntityInterface $page_layout): array {
    if (!$page_layout) {
      return [(string) $this->configFactory->get('system.theme')->get('default')];
    }
    $themes = $page_layout->getDependencies()['theme'] ?? [];
    $sources = \method_exists($page_layout, 'getSources') ? $page_layout->getSources() : [];
    \array_walk_recursive($sources, function ($value, $key) use (&$themes): void {
      if ($key === 'component_id' && \is_string($value) && \str_contains($value, ':')) {
        $provider = \strstr($value, ':', TRUE);
        if ($this->themeHandler->themeExists($provider)) {
          $themes[] = $provider;
        }
      }
    });
    return $themes ? \array_values(\array_unique($themes)) : [(string) $this->configFactory->get('system.theme')->get('default')];
  }

}
