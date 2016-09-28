<?php
/**
 * @file
 * RouteSubscriber blocking routes that should not be accessible any more.
 */

namespace Drupal\auth0\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    // Block user.register, user.pass and user.reset routes.
    if ($route = $collection->get('user.register')) {
      $route->setRequirement('_access', 'FALSE');
    }

    if ($route = $collection->get('user.pass')) {
      $route->setRequirement('_access', 'FALSE');
    }

  }

}