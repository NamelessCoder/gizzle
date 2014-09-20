Gizzle
======

[![Build Status](https://travis-ci.org/NamelessCoder/gizzle.svg?branch=master)](https://travis-ci.org/NamelessCoder/gizzle)

A tiny GitHub Webhook Listener which can be easily extended with plugins.

Installing
----------

Run:

```bash
git clone https://github.com/NamelessCoder/gizzle.git && cd gizzle
composer install
```

And add a (publicly accessible!) virtual host for your favorite HTTP server pointing to `./web/` inside this folder or simply include the package in your own composer application.

Finally, configure your GitHub repository and add the URL to your virtual host, pointing to the file `/github-webhook.php`.

Security
--------

A default rule is implemented which completely prevents access to processing the payload data if the client does not originate from one of GitHub's official IP addresses. You can **disable** this enforcement by running:

```php
$gizzle = new \NamelessCoder\Gizzle\Payload($payloadData);
$gizzle->disableSecurityBecauseSecurityIsHandledInMyApplicationOrDaemon();
```

Yes, the method name is completely intentional - leaving this publicly accessible can, depending on which plugins you use/write, constitute a major security hole. If you require additional security such as tokens, this must be implemented around this libarry or as plugins (which should of course always be loaded **first** in your list of plugins).

Running
-------

The `./web/github-webhook.php` file which is shipped with this repository can be used as URL of your web hook when configuring it in GitHub - or you can manually process the payload from within your own application and use its URL instead:

```php
$gizzle = new \NamelessCoder\Gizzle\Payload($payloadData);
$gizzle->loadPlugins('MyVendor\\MyPackage');
// alternative loading 1: $gizzle->loadPlugins($arrayOfPackageNames);
// alternative loading 2: $gizzle->loadPlugins($package1, $package2, $package3);
/** @var \NamelessCoder\Gizzle\Response $response */
$response = $gizzle->run();
/** @var \NamelessCoder\Gizzle\Error[] $errors */
$errors = $response->getErrors();
/** @var integer $code */
$code = $response->getExitCode();
// code >0 indicates errors are present; value indicates exact error. Code =0 means no errors.
```

To create a plugin for Gizzle you need two classes:

1. The class `MyVendor\MyPackage\GizzlePlugins\PluginList` which must implement interface `NamelessCoder\Gizzle\PluginListInterface` and contain a `getPluginClassNames` method which returns an array of any number of string class names of your plugins.
2. The class `MyVendor\MyPackage\GizzlePlugins\MyAwesomePlugin` (or another class name or namespace location - your choice) which implements `NamelessCoder\Gizzle\PluginInterface` and the methods it specifies.

You can control which plugins should be loaded from your `PluginList` class, for example toggling each plugin by some configuration option, and returning your plugin class name as part of the array of class names returned by the `PluginList`. Each plugin is then matched with the GitHub payload data to determine if it needs to be executed - and if it does, a simple method is executed inside which you have access to an entity model instance with all meta data contained in the payload as nice entity objects.

Simply create the class, include the package name which contains the class when running $gizzle->loadPlugins().


