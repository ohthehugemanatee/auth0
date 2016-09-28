<?php
/**
 * @file
 * Page controller for receiving the login callback from Auth0.
 */

namespace Drupal\auth0\Controller;


use Auth0\SDK\Auth0;
use Drupal\auth0\Auth0Events;
use Drupal\auth0\Auth0UserEvent;
use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Utility\Random;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Routing\UrlGeneratorTrait;
use Drupal\Core\Site\Settings;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\externalauth\ExternalAuth;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Page controller for processing login callbacks from Auth0.
 *
 * @see \Drupal\Core\DependencyInjection\ContainerInjectionInterface
 */
class LoginController implements ContainerInjectionInterface {
  use UrlGeneratorTrait;
  use StringTranslationTrait;

  /**
   * Constructs a new LoginController.
   *
   * @param \Drupal\Core\Site\Settings $settings
   * @param \Drupal\Core\Logger\LoggerChannelFactory $loggerFactory
   * @param \Drupal\externalauth\ExternalAuth $externalAuth
   */
  public function __construct(Settings $settings, LoggerChannelFactory $loggerFactory, ExternalAuth $externalAuth, EventDispatcherInterface $event_dispatcher) {
    $this->settings = $settings->get('auth0');
    $this->logger = $loggerFactory->get('auth0');
    $this->externalAuth = $externalAuth;
    $this->eventDispatcher = $event_dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('settings'),
      $container->get('logger.factory'),
      $container->get('externalauth.externalauth'),
      $container->get('event_dispatcher')
    );
  }

  /**
   * Event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * The configuration for Auth0, defined in settings.php.
   *
   * @var \Drupal\Core\Site\Settings
   */
  protected $settings;

  /**
   * The logger channel for Auth0.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * ExternalAuth's convenience function service.
   *
   * @var \Drupal\externalauth\ExternalAuth
   */
  protected $externalAuth;

  public function loginCallback() {

    // If Auth0 is disabled in settings.php, return to the login route.
    if ($this->settings['disabled'] == TRUE) {
      return $this->redirect('user.login');
    }

    // Connect to Auth0 with credentials from settings.php
    $client_config = $this->settings['client'];
    $auth0 = new Auth0($client_config);

    $user_info = NULL;

    // Try to get user information from the SDK.
    try {
      $user_info = $auth0->getUser();
    }
    catch (\Exception $e) {
      $this->logger->error('Error occurred while getting the Auth0 user info or ID token: @exception', array('@exception' => $e->getMessage()));
    }

    // Respond to success or failure.
    $drupal_account = NULL;
    if ($user_info) {
      // Try logging into Drupal using ExternalAuth.
      $drupal_account = $this->loginRegister($user_info);
    }
    if ($drupal_account) {
      $this->eventDispatcher->dispatch(Auth0Events::USER_LOGIN, new Auth0UserEvent($drupal_account, $auth0));
    }
    else {
      drupal_set_message($this->t('There was a problem logging you in, please try again later.'), 'error');
    }

    return $this->redirect('user.page');
  }

  /**
   * Process Drupal login/registration after a successful return from Auth0.
   *
   * @param array $user_info  The Auth0 user.
   */
  private function loginRegister($user_info) {
    // Login/Register the user with Drupal as necessary, using Auth0's user_id as an identifier.
    $random = new Random();
    $mail = isset($user_info['email']) ? $user_info['email'] : $random->name(). "@example.com";
    $values = array(
        'mail' => $mail,
        'name' => $mail,
    );
    $drupal_account = $this->externalAuth->loginRegister($user_info['user_id'], 'auth0', $values);

    $drupal_account->save();
    return $drupal_account;
  }

}