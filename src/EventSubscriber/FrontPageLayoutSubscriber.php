<?php

declare(strict_types=1);

namespace Drupal\webadmin\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Render\PageDisplayVariantSelectionEvent;
use Drupal\Core\Render\RenderEvents;
use Drupal\Core\Theme\ThemeManagerInterface;
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
 * of its own theme and its block layout.
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
   * Selects the block page when another theme renders a page layout page.
   *
   * @param \Drupal\Core\Render\PageDisplayVariantSelectionEvent $event
   *   The event to process.
   */
  public function onSelectPageDisplayVariant(PageDisplayVariantSelectionEvent $event): void {
    if ($event->getPluginId() !== self::PAGE_LAYOUT_VARIANT) {
      return;
    }
    $event->addCacheContexts(['theme']);
    if (!\in_array($this->themeManager->getActiveTheme()->getName(), $this->layoutThemes(), TRUE)) {
      $event->setPluginId('block_page');
    }
  }

  /**
   * The themes the page layout of the current page is built for.
   *
   * A page layout is built with the components of a theme (like
   * "ui_suite_uikit:navbar"), and can depend on that theme. One without either
   * is built for the default theme.
   *
   * @return string[]
   *   The machine names of the themes.
   */
  private function layoutThemes(): array {
    $access_control = $this->entityTypeManager->getAccessControlHandler('page_layout');
    $page_layout = \method_exists($access_control, 'loadCurrentPageLayout') ? $access_control->loadCurrentPageLayout() : NULL;
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
