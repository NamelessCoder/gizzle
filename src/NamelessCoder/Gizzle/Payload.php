<?php

/**
 * This file belongs to the NamelessCoder/Gizzle package
 *
 * Copyright (c) 2014, Claus Due
 *
 * Released under the MIT license, of which the full text
 * was distributed with this package in file LICENSE.txt
 */

namespace NamelessCoder\Gizzle;

use Milo\Github\Api;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Payload
 */
class Payload extends JsonDataMapper {

	/**
	 * @var string
	 */
	protected $settingsFile = NULL;

	/**
	 * @var array
	 */
	protected $propertyMap = array(
		'commit' => 'head',
		'compare' => 'comparisonUrl',
		'after' => 'parent',
		'before' => 'child',
		'head_commit' => 'head',
		'ref_name' => 'refName'
	);

	/**
	 * @var array
	 */
	protected $propertyClasses = array(
		'branches' => 'NamelessCoder\\Gizzle\\Branch[]',
		'commits' => 'NamelessCoder\\Gizzle\\Commit[]',
		'head' => 'NamelessCoder\\Gizzle\\Commit',
		'sender' => 'NamelessCoder\\Gizzle\\Entity',
		'repository' => 'NamelessCoder\\Gizzle\\Repository'
	);

	/**
	 * @var array
	 */
	protected $branches = array();

	/**
	 * @var string
	 */
	protected $context = NULL;

	/**
	 * @var string
	 */
	protected $parent = NULL;

	/**
	 * @var string
	 */
	protected $child = NULL;

	/**
	 * @var Commit[]
	 */
	protected $commits = NULL;

	/**
	 * @var string
	 */
	protected $comparisonUrl = NULL;

	/**
	 * @var boolean
	 */
	protected $created = NULL;

	/**
	 * @var boolean
	 */
	protected $deleted = NULL;

	/**
	 * @var boolean
	 */
	protected $forced = NULL;

	/**
	 * @var Commit
	 */
	protected $head = NULL;

	/**
	 * @var Entity
	 */
	protected $organization = NULL;

	/**
	 * @var Entity
	 */
	protected $sender = NULL;

	/**
	 * @var string
	 */
	protected $ref = NULL;

	/**
	 * @var string
	 */
	protected $refName = NULL;

	/**
	 * @var Repository
	 */
	protected $repository = NULL;

	/**
	 * @var PluginInterface[]
	 */
	protected $plugins = array();

	/**
	 * @var Response
	 */
	protected $response = NULL;

	/**
	 * @var Api
	 */
	protected $api = NULL;

	/**
	 * Payload creation
	 *
	 * Contains first-entry validations and error throwing
	 * if an inconsistent environment is detected, preventing
	 * usage of Payload outside the real (or a properly
	 * emulated) context.
	 *
	 * CLI usage is exempt from validating the payload data
	 * using SHA1 hashing; HTTP requests are not.
	 *
	 * @param string $jsonData
	 * @param string $secret
	 */
	public function __construct($jsonData, $secret, $settingsFile = 'settings/SelfUpdate.yml') {
		if (FALSE === defined('GIZZLE_HOME') || FALSE === is_dir(GIZZLE_HOME)) {
			throw new \RuntimeException('Constant GIZZLE_HOME must be set to a valid directory path, Payload rejected.', 1412207023);
		}
		$this->response = new Response();
		$this->settingsFile = $settingsFile;
		if (FALSE === $this->isCommandLine()) {
			$this->validate($jsonData, $secret);
		}
		$this->map($jsonData);
	}

	/**
	 * @return boolean
	 */
	protected function isCommandLine() {
		return 'cli' === php_sapi_name();
	}

	/**
	 * @param $jsonData
	 * @param $secret
	 * @return void
	 */
	protected function validate($jsonData, $secret) {
		$signatureHeader = $this->readSignatureHeader();
		list ($algorithm, $hash) = explode('=', $signatureHeader);
		$calculatedHash = hash_hmac($algorithm, $jsonData, $secret);
		if ($calculatedHash !== $hash) {
			throw new \RuntimeException('Invalid request hash - please make sure you entered the correct "secret"', 1411225210);
		}
	}

	/**
	 * @return string|NULL
	 */
	protected function readSignatureHeader() {
		return TRUE === isset($_SERVER['HTTP_X_HUB_SIGNATURE']) ? $_SERVER['HTTP_X_HUB_SIGNATURE'] : NULL;
	}

	/**
	 * @param mixed $_
	 * @return self
	 */
	public function loadPlugins($_) {
		$arguments = func_get_args();
		foreach ($arguments as $possiblePackage) {
			if (FALSE === is_array($possiblePackage)) {
				$this->plugins += $this->loadPluginsFromPackage($possiblePackage);
			} else {
				foreach ($possiblePackage as $package) {
					$this->plugins += $this->loadPluginsFromPackage($package);
				}
			}
		}
		return $this;
	}

	/**
	 * @param string $package
	 * @return PluginInterface[]
	 */
	protected function loadPluginsFromPackage($package) {
		$plugins = array();
		$settings = $this->loadSettings();
		$expectedListerClassName = '\\' . $package . '\\GizzlePlugins\\PluginList';
		if (TRUE === class_exists($expectedListerClassName)) {
			$packageSettings = (array) TRUE === isset($settings[$package]) ? $settings[$package] : array();
			/** @var PluginListInterface $lister */
			$lister = new $expectedListerClassName();
			$lister->initialize($settings);
			$pluginClassNames = $lister->getPluginClassNames();
			$pluginClassNames = array_combine($pluginClassNames, array_fill(0, count($pluginClassNames), array()));
			$pluginSettings = (array) (TRUE === isset($settings[$package]) ? $settings[$package] : $pluginClassNames);
			$packagePlugins = $this->loadPluginInstances($pluginSettings);
			$plugins = array_merge($plugins, $packagePlugins);
		}
		return $plugins;
	}

	/**
	 * @param array $pluginClassNamesAndSettings
	 * @return array
	 */
	protected function loadPluginInstances(array $pluginClassNamesAndSettings) {
		$plugins = array();
		foreach ($pluginClassNamesAndSettings as $class => $settings) {
			$plugins[] = $this->loadPluginInstance(
				$class,
				(array) $settings
			);
		}
		return $plugins;
	}

	/**
	 * @param string $pluginClassName
	 * @param array $settings
	 * @return PluginInterface
	 */
	protected function loadPluginInstance($pluginClassName, array $settings) {
		$pluginClassName = '\\' . ltrim($pluginClassName, '\\');
		/** @var PluginInterface $plugin */
		$plugin = new $pluginClassName();
		$plugin->initialize($settings);
		return $plugin;
	}

	/**
	 * @return array
	 */
	protected function loadSettings() {
		$folder = realpath(__DIR__);
		$segments = explode('/', $folder);
		$file = NULL;
		while (NULL === $file && 0 < count($segments) && ($segment = array_pop($segments))) {
			$base = '/' . implode('/', $segments) . '/' . $segment . '/';
			$expectedFile = $base . $this->settingsFile;
			$expectedFile = FALSE === file_exists($expectedFile) ? $base . 'Settings.yml' : $expectedFile;
			$file = TRUE === file_exists($expectedFile) ? $expectedFile : NULL;
		}
		return (array) (TRUE === file_exists($file) ? Yaml::parse($file) : array());
	}

	/**
	 * @return Response
	 */
	public function process() {
		if (0 === count($this->plugins)) {
			// load plugins which are configured in Settings.yml
			$settings = $this->loadSettings();
			$packages = array_keys($settings);
			$this->loadPlugins($packages);
		}
		$this->executePlugins($this->plugins);
		return $this->response;
	}

	/**
	 * @param PluginInterface[] $plugins
	 */
	protected function executePlugins(array $plugins) {
		$errors = array();
		foreach ($plugins as $plugin) {
			if (TRUE === $plugin->trigger($this)) {
				$pluginErrors = $this->executePlugin($plugin);
				$errors = array_merge($errors, $pluginErrors);
			}
		}
		if (0 < count($errors)) {
			$this->response->setCode(1);
			$this->response->setErrors($errors);
		}
	}

	/**
	 * @param PluginInterface $plugin
	 * @return array
	 */
	protected function executePlugin(PluginInterface $plugin) {
		$errors = array();
		try {
			$this->dispatchPluginEvent($plugin, AbstractPlugin::OPTION_EVENTS_ONSTART);
			$plugin->process($this);
			$this->dispatchPluginEvent($plugin, AbstractPlugin::OPTION_EVENTS_ONSUCCESS);
		} catch (\RuntimeException $error) {
			$this->dispatchPluginEvent($plugin, AbstractPlugin::OPTION_EVENTS_ONSTART);
			$errors[] = $error;
		}
		return $errors;
	}

	/**
	 * @param PluginInterface $plugin
	 * @param string $eventSetting
	 */
	protected function dispatchPluginEvent(PluginInterface $plugin, $eventSetting) {
		$events = $plugin->getSetting($eventSetting);
		if (TRUE === is_array($events)) {
			$plugins = $this->loadPluginInstances($events);
			foreach ($plugins as $eventPlugin) {
				try {
					$this->executePlugin($eventPlugin);
				} catch (\RuntimeException $error) {
					$this->response->addOutputFromPlugin($plugin, array('Event error! Message: ' . $error->getMessage()));
				}
			}
		}
	}

	/**
	 * @param Branch[] $branches
	 * @return void
	 */
	public function setBranches($branches) {
		$this->branches = $branches;
	}

	/**
	 * @return Branch[]
	 */
	public function getBranches() {
		return $this->branches;
	}

	/**
	 * @param string $context
	 */
	public function setContext($context) {
		$this->context = $context;
	}

	/**
	 * @return string
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 * @param string $parent
	 */
	public function setParent($parent) {
		$this->parent = $parent;
	}

	/**
	 * @return string
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * @param string $child
	 */
	public function setChild($child) {
		$this->child = $child;
	}

	/**
	 * @return string
	 */
	public function getChild() {
		return $this->child;
	}

	/**
	 * @param Commit[] $commits
	 */
	public function setCommits(array $commits) {
		$this->commits = $commits;
	}

	/**
	 * @return Commit[]
	 */
	public function getCommits() {
		return $this->commits;
	}

	/**
	 * @param string $comparisonUrl
	 */
	public function setComparisonUrl($comparisonUrl) {
		$this->comparisonUrl = $comparisonUrl;
	}

	/**
	 * @return string
	 */
	public function getComparisonUrl() {
		return $this->comparisonUrl;
	}

	/**
	 * @param boolean $created
	 */
	public function setCreated($created) {
		$this->created = $created;
	}

	/**
	 * @return boolean
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @param boolean $deleted
	 */
	public function setDeleted($deleted) {
		$this->deleted = $deleted;
	}

	/**
	 * @return boolean
	 */
	public function getDeleted() {
		return $this->deleted;
	}

	/**
	 * @param boolean $forced
	 */
	public function setForced($forced) {
		$this->forced = $forced;
	}

	/**
	 * @return boolean
	 */
	public function getForced() {
		return $this->forced;
	}

	/**
	 * @param Commit $head
	 */
	public function setHead(Commit $head) {
		$this->head = $head;
	}

	/**
	 * @return Commit
	 */
	public function getHead() {
		return $this->head;
	}

	/**
	 * @param Entity $organization
	 * @return void
	 */
	public function setOrganization(Entity $organization) {
		$this->organization = $organization;
	}

	/**
	 * @return Entity
	 */
	public function getOrganization() {
		return $this->organization;
	}

	/**
	 * @param Entity $sender
	 */
	public function setSender(Entity $sender) {
		$this->sender = $sender;
	}

	/**
	 * @return Entity
	 */
	public function getSender() {
		return $this->sender;
	}

	/**
	 * @param string $ref
	 */
	public function setRef($ref) {
		$this->ref = $ref;
	}

	/**
	 * @return string
	 */
	public function getRef() {
		return $this->ref;
	}

	/**
	 * @param string $refName
	 */
	public function setRefName($refName) {
		$this->refName = $refName;
	}

	/**
	 * @return string
	 */
	public function getRefName() {
		return $this->refName;
	}

	/**
	 * @param Repository $repository
	 */
	public function setRepository(Repository $repository) {
		$this->repository = $repository;
	}

	/**
	 * @return Repository
	 */
	public function getRepository() {
		return $this->repository;
	}

	/**
	 * @return Response
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * @param Api $api
	 * @return void
	 */
	public function setApi(Api $api) {
		$this->api = $api;
	}

	/**
	 * @return Api
	 */
	public function getApi() {
		return $this->api;
	}

}
