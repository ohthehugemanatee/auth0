# Auth0 for Drupal 8

A Drupal 8 module to provide external authentication through Auth0

## Installation

* Set up your Application in Auth0.
* Install the module normally.
* Set your API credentials in settings.php, like this:

```
$settings['auth0']['disabled'] = FALSE;
$settings['auth0']['client'] = array(
  'domain'        => 'foo-bar.eu.auth0.com',
  'client_id'     => 'fooCCx3123jjsflj5rSHgsLT9OY7',
  'client_secret' => 'bar3qqoj62UxDEznSFJvm_GOqpDOAns7EsfyvT15K_4nQudgfaa430GfRnhe4Ek',
  'redirect_uri'  => 'http://example.com/auth0/login'
);
```

Note that redirect_uri must end in auth0/login!

Though this module takes over the Drupal login form, it does not block 
access to account registration. So you probably want to add 
```
$settings['register'] = 'admin_only';
```
to your settings.php


## Disabling (for local, development sites etc)

Add one line to your settings.php to disable Auth0 integration:

```
$settings['auth0']['disabled'] = TRUE;
```
