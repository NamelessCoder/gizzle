Gizzle
======

[![Build Status](https://travis-ci.org/NamelessCoder/gizzle.svg?branch=master)](https://travis-ci.org/NamelessCoder/gizzle) [![Coverage Status](https://img.shields.io/coveralls/NamelessCoder/gizzle.svg)](https://coveralls.io/r/NamelessCoder/gizzle) [![Reference Status](https://www.versioneye.com/php/namelesscoder:gizzle/reference_badge.svg)](https://www.versioneye.com/php/namelesscoder:gizzle/references) [![Dependency Status](https://www.versioneye.com/user/projects/5427fddb8200670d1400003b/badge.svg)](https://www.versioneye.com/user/projects/5427fddb8200670d1400003b) [![Latest Stable Version](https://poser.pugx.org/namelesscoder/gizzle/v/stable.svg)](https://packagist.org/packages/namelesscoder/gizzle) [![Total Downloads](https://poser.pugx.org/namelesscoder/gizzle/downloads.svg)](https://packagist.org/packages/namelesscoder/gizzle)

A tiny GitHub Webhook Listener which can be easily extended with plugins.

Installing
----------

Run:

```bash
composer require namelesscoder/gizzle
```

Assuming your project uses the composer class loader you then have access to using Gizzle's classes in your project.

### Virtual Host

If you wish to use the default receiving script, symlink the `web` folder from Gizzle:

```bash
ln -s vendor/namelesscoder/gizzle/web
```

And add a (publicly accessible!) virtual host for your favorite HTTP server pointing to `./web/` inside this folder or simply include the package in your own composer application.

Finally, configure your GitHub repository and add the URL to your Gizzle project pointing to the file `/github-webhook.php`.

You can of course create your own receiving script instead of `github-webhook.php` if you need additional features. The default receiving script supports the features described in this README.

### Security (secret file and personal token)

Gizzle uses the `secret` (a token) which you enter in GitHub while setting up the web hook. Use the same secret token when initializing your Payload class. The secret token is required and must match, or the Payload throws a RuntimeException.

```php
$data = file_get_contents('php://input');
$secret = 'mysecret';
$gizzle = new \NamelessCoder\Gizzle\Payload($data, $secret);
```

**When using the shipped public file in `./web/github-webhook.php` your secret token will be read from the file `./.secret`** (note: dot-file, placed outside the public web root). When first installing this package or your own package which uses this package, create the file whichever way you prefer and make sure it contains your "secret" key from GitHub. For example, using the shell:

```bash
cd /path/to/gizzle-or-your-package/
echo "mysupersecretkey" > .secret
```

Gizzle can also use a personal access token to "talk back" to GitHub. You can utilize this to do things like comment on the commit and update it with a status (which is displayed when the commit is part of a pull request) - and even automatically merge branches. The default implementation for every plugin will _automatically update the commit status with pending, success or error as the Payload execution progresses_, but other plugins can access the GitHub API from Gizzle plugins. All you have to do is make sure a special `.token` file exists and contains your personal access token - which, like the `.secret` file - is sensitive information you should never share. The `.token` file is created the exact same way as the `.secret` file and you can obtain a new access token to use just with Gizzle. [Read more about how in the section about the GitHub API integration](#updating-commit-status).

Running
-------

The `./web/github-webhook.php` file which is shipped with this repository can be used as URL of your web hook when configuring it in GitHub - or you can manually process the payload from within your own application and use its URL instead:

```php
$data = file_get_contents('php://input');
$secret = 'mysecret';
$gizzle = new \NamelessCoder\Gizzle\Payload($data, $secret);
// Plugins are then loaded from the packages used in, and in the order of, Settings.yml (see below)
// alternative loading 1: $gizzle->loadPlugins('MyVendor\\MyPackage');
// alternative loading 2: $gizzle->loadPlugins($arrayOfPackageNames);
// alternative loading 3: $gizzle->loadPlugins($package1, $package2, $package3);
// using either alternative causes the settings-based loading to be skipped, but settings are still used.

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
Vendor\PackageName:
  Vendor\PackageName\GizzlePlugins\OnePlugin:
    enabled: true
  AnotherVendor\AnotherPackageName\GizzlePlugins\OtherPlugin:
    enabled: true
OtherVendor\ThirdPackage:
  OtherVendor\ThirdPackage\GizzlePlugins\ThirdPlugin:
    enabled: true
```

It is possible for any Package to return Plugins from other packages as well - which is ideal when constructing Packages which include and provide configuration for many plugins. Such a package might return plugin names which **don't belong to that Package**, and because the class name is used as key in the configuration, **it is possible to configure the same Plugin differently when it is provided by Package A and Package B.** This is done to increase reusability of smaller plugins which can serve many purposes - a good example would be a Git plugin which pushes when used in one package and pulls when used in another, because each package can provide a different configuration for the same plugin.

Word of caution, though: the plugin can **only** be configured inside the scope of the Package that returned it. This means that **every** mandatory option should be present in each place the Plugin is used (in other words: you cannot configure global defaults).

### Plugin events

Gizzle provides you with a way to configure for each plugin, a list of other plugins which should be executed with the same payload on special events. Currently supported is the `onStart`, `onSuccess` and `onError` events. There are two ways of configuring events; one is from inside your custom plugin classes by providing a default value for the corresponding setting (you can do so in the `initialize()` or `getSetting` methods whichever you prefer) - the other way is defining these settings in your configuration file. If providing these as a default setting value from your custom plugin simply return the exact same array that is expressed for each event in this example.

About the example: the illustration configures the self-update plugin which runs `git pull` and `composer install` in the root folder of the package that implemented it in its configuration. The sub-plugin that's also used is _a ficticious plugin which sends an email to the entity pushing the commit described in the payload with cc: or bcc: defined by settings for that plugin_ and another _also ficticious plugin which is able to send a message to `syslog` with a custom severity_. It also demonstrates how you can use this in as many nested levels as you wish, catching events at each level; in this case causing the "CRITICAL!" syslog message if self-update failed and either the pusher or the master dev-op could not be notified of the failure directly.

```yaml
MyVendor\MyGizzleImplementingPackage:
  NamelessCoder\Gizzle\GizzlePlugins\SelfUpdatePlugin:
    onStart:
      # send a copy to pusher only, informing that self-update began
      OtherVendor\GizzleEmailPlugins\GizzlePlugins\EmailPusherPlugin:
        subject: Starting self-update of MyGizzleImplementingPackage
    onError:
      # send a copy to pusher and bcc an address that won't be disclosed to pusher
      OtherVendor\GizzleEmailPlugins\GizzlePlugins\EmailPusherPlugin:
        subject: Your team updated a service, please validate the result.
        bcc: master-devops@organization.foo
        onError:
          OtherVendor\MonitoringPlugins\GizzlePlugins\LogPlugin:
            message: CRITICAL ERROR! Man all battle stations, the server can't send mail!
            severity: fatal
    onSuccess:
      # send a copy to pusher with cc to all developers
      OtherVendor\GizzleEmailPlugins\GizzlePlugins\EmailPusherPlugin:
        subject: MyGizzleImplementingPackage was updated!
        cc: developers@organization.foo
```

Note that these events are _defined on the same level as any other setting a plugin uses_ which means the names are reserved and cannot be used as names for your own plugin settings. Defining an incorrect value (something not an array) may cause exceptions. However: contrary to how "root" plugins report errors, exceptions which are raised by a plugin that is used as event listener _will simply add a friendly error message to the response, allowing the next plugin to continue_, and so on. Whereas a "root" plugin would cause the entire Payload to stop processing.

### Multiple configurations

You can create any number of alternative configuration files. The name or path of the settings file can be provided manually when instanciating the Payload:

```php
$settingsFile = 'Settings/SpecialSettings.yml';
$payload = new Payload($data = '{}', $secret = '', $settingsFile);
```

You can then place any number of such additional settings files as `./Settings/*.yml` and reference them by changing the third parameter.

The default implementation that ships with Gizzle - the `github-webhook.php` file inside `./web/` - uses the `$_GET['settings']` argument as third parameter after validating that it contains the only allowed characters: `a-z`, `A-Z`, `0-9` and `/`. The latter is allowed in order to let you divide configuration files into any number of sub directories and reference them by path. Additionally, the file name itself must end with `.yml`, must not be a hidden file (dot-file) and finally must be a relative path (e.g. not starting with `/`).

To select which configuration gets used simply set the expected GET parameter when adding the URL as webhook in GitHub's repository settings:

```
http://mydomain.foo/github-webhook.php?settings=Settings/SpecialSettings.yml
```

And you can specify multiple settings files which must all be processed by specifying arguments as an array:

```
http://mydomain.foo/github-webhook.php?settings[]=Settings/SpecialSettings.yml&settings[]=Settings/OtherSettings.yml
```

Which causes first `./Settings/SpecialSettings.yml` and then `./Settings/OtherSettings.yml` to be processed in the same execution. Any error caused by one will cause the job to exit, throwing only the first error which occurred.

You can also use this to version your settings. If for example your design practices change and you require support for more than one repository design pattern, you can easily store the legacy configuration as a different settings file and by modifying the web hook URL in each repository, support both of your repositories' patterns simultaneously. A good example of when such versioning might become necessary is when switching to/from the "git flow" pattern or in multiple production branch scenarios where new production branches are continuously added and removed.

Updating commit status
----------------------

If you wish to make Gizzle update the HEAD commit of the Payload as it gets processed (one status per settings file that processes the Payload), Gizzle supports a GitHub personal access token which, like the `.secret` file, is placed in the project root folder and is named `.token`. A token is a 32-character string with randomized letters and numbers.

To obtain a personal access token:

1. In GitHub, under account settings, [generate yourself a new Access Token](https://github.com/settings/applications#personal-access-tokens).
2. Make sure you associate this token with at least permissions to access status and access (public) repositories. If any plugins require additional permissions, each should document which - and you should add permissions as required.
3. Copy the token and insert it in the file `.token` in the project root folder.
4. Do not commit the token file! Instead, add it to git ignore either locally or for your project. The token is **sensitive information** and should never be shared.

When present, the token is read from this file and used to initialize the [GitHub API used by Gizzle](https://github.com/milo/github-api). The API can then be used by Gizzle and Gizzle plugins to perform any action which you permit.

Resources:

* [Access Token generation in GitHub account](https://github.com/settings/applications#personal-access-tokens)
* [Documentation for every possible action you can perform through this GitHub API](https://developer.github.com/v3/)
* [Documentation for how to use the PHP `Api` class to access the GitHub API](https://github.com/milo/github-api/wiki)

### Usage of API from plugins

The API can be accessed via the `$payload` argument that is provided to the essential methods on plugins. To access the API:

```php
$api = $payload->getApi();
if (TRUE === empty($api->getToken()) {
	// avoid doing anything with the API when there is
	// no token loaded; UNLESS you are able to provide
	// your own token from inside the plugin code.
} else {
	$response = $api->get('/emoji');
	$emojis = $api->decode($response);
}
```

Every resource type in Github has its own URL and some resource types support different parameters depending on the context - for example, a Github "comment" can be created in several different contexts and each has requirements for required properties. Study the [Github API v3 developer documentation](https://developer.github.com/v3/) for the details about each resource.

Note the additional decoding step which is required when you need to read data from the response. An `stdClass` is returned with public properties allowing you to read response data - see the official GitHub v3 API reference for available data for each action. If you need another data type, manually use `json_decode($response->getContent());` and pass any special `JSON_*` options you require.

> Tip: If the API returns data which contains for example commits, repositories, entities etc. and the data type is `array`, Gizzle's domain model supports mapping such data (recursively) by simply passing the array of properties into the constructor: `$commit = new Commit($commitDataAsArray);`.

Creating plugins
----------------

To create a plugin for Gizzle you need one mandatory class and optionally a lister class (which gets shared by all plugins in your package):

1. The class `MyVendor\MyPackage\GizzlePlugins\MyAwesomePlugin` (or another class name or namespace location - your choice) which implements `NamelessCoder\Gizzle\PluginInterface` and the methods it specifies.
2. Optionally also the class `MyVendor\MyPackage\GizzlePlugins\PluginList` which must implement interface `NamelessCoder\Gizzle\PluginListInterface` and contain a `getPluginClassNames` method which returns an array of any number of string class names of your plugins. This class gets used when users reference your plugin collection using your package name - it is not required if your plugins are solely intended to be used from a `Settings.yml` context (as described above).

When users load your plugins by package name, your PluginList class is asked to return the class names of plugins and it is here you have your option to change which class names are returned for example depending on configuration. However, when users implement your plugin directly in `Settings.yml` or by manually instanciating it, your PluginList class does not get used. This means that _if your plugin only should be used directly from settings or manually inside other plugins, you do not need the PluginList class - which is why it is marked optional_.

Example plugin
--------------

```php
<?php
namespace NamelessCoder\Gizzle\GizzlePlugins;

use NamelessCoder\Gizzle\PluginInterface;
use NamelessCoder\Gizzle\AbstractPlugin;


/**
 * Example Gizzle Plugin
 *
 * Sends an email to the person who pushed the commit,
 * but only if the commit was made to the "demo" branch.
 */
class ExamplePlugin extends AbstractPlugin implements PluginInterface {

	/**
	 * Returns TRUE to trigger this Plugin if branch
	 * is "demo", as determined by REF of Payload.
	 *
	 * @param Payload $payload
	 * @return boolean
	 */
	public function trigger(Payload $payload) {
		return 'refs/heads/demo' === $payload->getRef();
	}

	/**
	 * Send a thank you email to commit pusher.
	 *
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
