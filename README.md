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

Gizzle uses the `secret` (a token) which you enter in GitHub while setting up the web hook. Use the same secret token when initializing your Payload class. The secret token is required and must match, or the Payload throws a RuntimeException.

```php
$data = file_get_contents('php://input');
$secret = 'mysecret';
$gizzle = new \NamelessCoder\Gizzle\Payload($data, $secret);
```

When using the shipped public file in `./web/github-webhook.php` your secret token will be read from the file `./.secret` (note: dot-file, placed outside the public web root).

Running
-------

The `./web/github-webhook.php` file which is shipped with this repository can be used as URL of your web hook when configuring it in GitHub - or you can manually process the payload from within your own application and use its URL instead:

```php
$data = file_get_contents('php://input');
$secret = 'mysecret';
$gizzle = new \NamelessCoder\Gizzle\Payload($data, $secret);
$gizzle->loadPlugins('MyVendor\\MyPackage');
// alternative loading 1: $gizzle->loadPlugins($arrayOfPackageNames);
// alternative loading 2: $gizzle->loadPlugins($package1, $package2, $package3);

/** @var \NamelessCoder\Gizzle\Response $response */
$response = $gizzle->process();

/** @var integer $code */
$code = $response->getCode();
// code >0 indicates errors are present; value indicates exact error. Code =0 means no errors.

/** @var \RuntimeException[] $errors */
$errors = $response->getErrors();
```

Configuring plugins
-------------------

Not all plugins support configuration but those that do can be configured by placing a `Settings.yml` file in the root of your project or in any folder leading up to the file which creates the Payload instance. The first file found in a reverse search gets used and should contain all the configuration required by your plugins.

The format of the configuration file is:

```yaml
Vendor\\PackageName:
  Vendor\\PackageName\\GizzlePlugins\\OnePlugin:
    enabled: true
  AnotherVendor\\AnotherPackageName\\GizzlePlugins\\OtherPlugin:
    enabled: true
OtherVendor\\ThirdPackage:
  OtherVendor\\ThirdPackage\\GizzlePlugins\\ThirdPlugin:
    enabled: true
```

It is possible for any Package to return Plugins from other packages as well - which is ideal when constructing Packages which include and provide configuration for many plugins. Such a package might return plugin names which **don't belong to that Package**, and because the class name is used as key in the configuration, **it is possible to configure the same Plugin differently when it is provided by Package A and Package B.** This is done to increase reusability of smaller plugins which can serve many purposes - a good example would be a Git plugin which pushes when used in one package and pulls when used in another, because each package can provide a different configuration for the same plugin.

Word of caution, though: the plugin can **only** be configured inside the scope of the Package that returned it. This means that **every** mandatory option should be present in each place the Plugin is used (in other words: you cannot configure global defaults).

Creating plugins
----------------

To create a plugin for Gizzle you need two classes:

1. The class `MyVendor\MyPackage\GizzlePlugins\PluginList` which must implement interface `NamelessCoder\Gizzle\PluginListInterface` and contain a `getPluginClassNames` method which returns an array of any number of string class names of your plugins.
2. The class `MyVendor\MyPackage\GizzlePlugins\MyAwesomePlugin` (or another class name or namespace location - your choice) which implements `NamelessCoder\Gizzle\PluginInterface` and the methods it specifies.

You can control which plugins should be loaded from your `PluginList` class, for example toggling each plugin by some configuration option, and returning your plugin class name as part of the array of class names returned by the `PluginList`. Each plugin is then matched with the GitHub payload data to determine if it needs to be executed - and if it does, a simple method is executed inside which you have access to an entity model instance with all meta data contained in the payload as nice entity objects.

Simply create the class, include the package name which contains the class when running $gizzle->loadPlugins().

Example plugin
--------------

```php
<?php
namespace NamelessCoder\Gizzle\GizzlePlugins;

/**
 * Example Gizzle Plugin
 *
 * Sends an email to the person who pushed the commit,
 * but only if the commit was made to the "demo" branch.
 */
class ExamplePlugin implements \NamelessCoder\Gizzle\PluginInterface {

	/**
	 * @param Payload $payload
	 * @return boolean
	 */
	public function trigger(Payload $payload) {
		return 'demo' === $payload->getRef();
	}

	/**
	 * @param Payload $payload
	 * @return void
	 * @throws \RuntimeException
	 */
	public function process(Payload $payload) {
		$pusherEmail = $payload->getPusher()->getEmail();
		$body = 'A big thank you from ' . $payload->getRepository()->getOwner()->getName();
		$mailed = mail($pusherEmail, 'Thank you for contributing!', $body);
		if (FALSE === $mailed) {
			throw new \RuntimeException('Could not email the kind contributor at ' . $pusherEmail);
		}
	}

}
```
