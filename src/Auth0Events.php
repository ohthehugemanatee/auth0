<?php

namespace Drupal\auth0;

/**
 * Defines events for the Auth0 module.
 */
final class Auth0Events {

  /**
   * The name of the event fired after a user successfully authenticates via Auth0.
   *
   * Receives a
   * \Drupal\language\Config\LanguageConfigOverrideCrudEvent instance.
   *
   * @Event
   *
   * @see \Drupal\language\Config\LanguageConfigOverrideCrudEvent
   * @see \Drupal\language\Config\LanguageConfigOverride::save()
   * @see \Drupal\locale\LocaleConfigSubscriber
   */
  const USER_LOGIN = 'auth0.user_login';
}