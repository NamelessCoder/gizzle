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
use Milo\Github\Http\Response as ApiResponse;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Payload
 *
 * For general usage instructions, see README.md
 */
class Payload extends JsonDataMapper {

	const OPTION_MAX_MESSAGES = 'maxMessages';
	const OPTION_MAX_MESSAGES_DEFAULT = 3;

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
		'ref_name' => 'refName',
		'pull_request' => 'pullRequest'
	);

	/**
	 * @var array
	 */
	protected $propertyClasses = array(
		'branches' => 'NamelessCoder\\Gizzle\\Branch[]',
		'commits' => 'NamelessCoder\\Gizzle\\Commit[]',
		'head' => 'NamelessCoder\\Gizzle\\Commit',
		'sender' => 'NamelessCoder\\Gizzle\\Entity',
		'repository' => 'NamelessCoder\\Gizzle\\Repository',
		'pullRequest' => 'NamelessCoder\\Gizzle\\PullRequest'
	);

	/**
	 * @var string
	 */
	protected $action = NULL;

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
	 * @var PullRequest
	 */
	protected $pullRequest = NULL;

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
	 * @var Message[]
	 */
	protected $messages = array();

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
	 * @param string $settingsFile
	 * @throws \RuntimeException
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
	 * Validates the data in $jsonData by checking that
	 * the hash of that data matches the hash calculated
	 * with the "secret" file that Gizzle uses.
	 *
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
	 * Returns the value of the HTTP_X_HUB_SIGNATURE
	 * header included in payloads coming from GitHub.
	 *
	 * @return string|NULL
	 */
	protected function readSignatureHeader() {
		return TRUE === isset($_SERVER['HTTP_X_HUB_SIGNATURE']) ? $_SERVER['HTTP_X_HUB_SIGNATURE'] : NULL;
	}

	/**
	 * Wrapper method to load plugins from an arbitrary
	 * source definition - either a string package name,
	 * or an array of string package names.
	 *
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
	 * Create and initialize all plugin classes from the
	 * package specified in the input argument. Similar
	 * to loading a list of plugin classes, but uses the
	 * special PluginList class from that package in order
	 * to determine which plugins should be activated and
	 * to deliver default settings values for those plugins.
	 *
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
			$lister->initialize($packageSettings);
			$pluginClassNames = $lister->getPluginClassNames();
			$pluginClassNames = array_combine($pluginClassNames, array_fill(0, count($pluginClassNames), array()));
			$pluginSettings = (array) (TRUE === isset($settings[$package]) ? $settings[$package] : $pluginClassNames);
			$packagePlugins = $this->loadPluginInstances($pluginSettings);
			$plugins = array_merge($plugins, $packagePlugins);
		}
		return $plugins;
	}

	/**
	 * Create and initialize all plugin classes based on an
	 * input array in which the keys are the class names of
	 * plugins and the values are an array of settings for
	 * that particular plugin instance.
	 *
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
	 * Create and initialize a single plugin instance
	 * based on class name and settings.
	 *
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
	 * Loads the settings specified in the `.yml` file
	 * that this Gizzle instance was instructed to use.
	 * If the file does not exist, no settings are loaded
	 * and no error message given.
	 *
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
	 * Run Gizzle plugins specified in configuration
	 * or input arguments. Return a Response object
	 * containing feedback from all plugins.
	 *
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
	 * @param integer $limit
	 * @param Message $overflowMessage
	 */
	public function dispatchMessages($limit = NULL, Message $overflowMessage = NULL) {
		if (NULL === $limit) {
			$settings = $this->loadSettings();
			$limit = TRUE === isset($settings[self::OPTION_MAX_MESSAGES]) ? $settings[self::OPTION_MAX_MESSAGES] : self::OPTION_MAX_MESSAGES_DEFAULT;
		}
		$numberOfMessages = count($this->messages);
		if ($limit >= $numberOfMessages) {
			foreach ($this->messages as $message) {
				$this->dispatchMessage($message);
			}
		} else {
			if (NULL === $overflowMessage) {
				$summary = $this->generateSummaryOfMessages();
				$messageString = 'Too many messages were triggered (%s - limit was %s). Summary: ' . PHP_EOL . PHP_EOL;
				$messageString = sprintf($messageString, $numberOfMessages, $limit, $summary);
				$overflowMessage = new Message($messageString);
			}
			$this->dispatchMessage($overflowMessage);
		}
	}

	/**
	 * @param Message $message
	 * @return ApiResponse|NULL
	 */
	protected function dispatchMessage(Message $message) {
		$api = $this->getApi();
		$data = $message->toGitHubApiDataArray();
		$commit = $message->getCommit();
		$pullRequest = $message->getPullRequest();
		$url = NULL;
		if (TRUE === $pullRequest instanceof PullRequest && NULL === $commit) {
			$url = $pullRequest->getUrlComments();
		} elseif (TRUE === $pullRequest instanceof PullRequest && TRUE === $commit instanceof Commit) {
			$url = $pullRequest->getUrlReviewComments();
		} elseif (TRUE === $commit instanceof Commit) {
			$url = $commit->getUrl();
		}
		if (NULL !== $url) {
			return $api->post($url, json_encode($data));
		}
		return NULL;
	}

	/**
	 * @param Message $message
	 */
	public function sendMessage(Message $message) {
		$lacksCommitAndPullRequest = (NULL === $message->getCommit() && NULL === $message->getPullRequest());
		if (TRUE === $lacksCommitAndPullRequest && TRUE === $this->pullRequest instanceof PullRequest) {
			$message->setPullRequest($this->pullRequest);
		} elseif (TRUE === $lacksCommitAndPullRequest && TRUE === $this->head instanceof Commit) {
			$message->setCommit($this->head);
		}
		$id = spl_object_hash($message);
		$this->messages[$id] = $message;
	}

	/**
	 * @return string
	 */
	protected function generateSummaryOfMessages() {
		$summary = '';
		foreach ($this->messages as $message) {
			$summary .= $this->generateSummaryOfMessage($message) . PHP_EOL . PHP_EOL;
		}
		return $summary;
	}

	/**
	 * @param Message $message
	 * @return string
	 */
	protected function generateSummaryOfMessage(Message $message) {
		$commit = $message->getCommit();
		$pullRequest = $message->getPullRequest();
		$preamble = '';
		if (TRUE === $commit instanceof Commit) {
			$preamble = 'Commit: ' . $commit->getId();
			$path = $message->getPath();
			if (NULL !== $path) {
				$preamble .= PHP_EOL;
				$preamble .= 'File: ' . $path . PHP_EOL;
				$preamble .= 'Line: ' . $message->getPosition();
			}
		} elseif (TRUE === $pullRequest instanceof PullRequest) {
			$preamble = 'Pull Request: ' . $pullRequest->getId();
		}
		$summary = $preamble . PHP_EOL . $message->getBody();
		return $summary;
	}

	/**
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * @param string $action
	 * @return void
	 */
	public function setAction($action) {
		$this->action = $action;
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
	 * @return PullRequest
	 */
	public function getPullRequest() {
		return $this->pullRequest;
	}

	/**
	 * @param PullRequest $pullRequest
	 * @return void
	 */
	public function setPullRequest(PullRequest $pullRequest) {
		$this->pullRequest = $pullRequest;
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
