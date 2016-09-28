<?php

namespace Drupal\auth0;

use Auth0\SDK\Auth0;
use Drupal\user\UserInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Wraps an Auth0 user event for subscribers.
 */
class Auth0UserEvent extends Event {

  /**
   * User object.
   *
   * @var \Drupal\User\UserInterface
   */
  protected $user;

  /**
   * Auth0 connection, already authenticated.
   */
  protected $auth0;

  /**
   * Constructs an Auth0 User event object.
   *
   * @param Auth0\SDK\Auth0
   *   Auth0 response
   */
  public function __construct(UserInterface $user, Auth0 $auth0) {
    $this->user = $user;
    $this->auth0 = $auth0;
  }

  /**
   * Gets user object.
   *
   * @return \Drupal\User\UserInterface
   */
  public function getUser() {
    return $this->user;
  }

  /**
   * Gets the Auth0 connection object.
   *
   * @return \Auth0\SDK\Auth0
   */
  public function getAuth0() {
    return $this->auth0;
  }
}
