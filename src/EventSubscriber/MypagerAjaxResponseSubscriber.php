<?php

namespace Drupal\mypager\EventSubscriber;

use Drupal\views\Ajax\ViewAjaxResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Response subscriber to handle AJAX responses.
 */
class MypagerAjaxResponseSubscriber implements EventSubscriberInterface {


  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [KernelEvents::RESPONSE => [['onResponse']]];
  }


  /**
   * Renders the ajax commands right before preparing the result.
   *
   * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
   *   The response event, which contains the possible AjaxResponse object.
   */
  public function onResponse(FilterResponseEvent $event) {
    $response = $event->getResponse();
    if (!($response instanceof ViewAjaxResponse)) {
      return;
    }
    $view = $response->getView();
    if ($view->getPager()
        ->getPluginId() !== 'mypager' || $view->getCurrentPage() === 0
    ) {
      return;
    }

    $commands = &$response->getCommands();
    $this->alterPaginationCommands($commands);
  }


  /**
   * Alter the views AJAX response commands only for the mypager.
   *
   * @param array $commands
   *   An array of commands to alter.
   */
  protected function alterPaginationCommands(&$commands) {
    foreach ($commands as $delta => &$command) {
      // Substitute the 'replace' method without our custom jQuery method which
      // will allow views content to be injected one after the other.
      if (isset($command['method']) && $command['method'] === 'replaceWith') {
        $command['method'] = 'mypagerInsertView';
      }
      // Stop the view from scrolling to the top of the page.
      if ($command['command'] === 'viewsScrollTop') {
        unset($commands[$delta]);
      }
    }
  }


}
