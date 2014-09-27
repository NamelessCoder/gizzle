<?php
namespace NamelessCoder\Gizzle;
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
		'before' => 'child'
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
	protected $forced;

	/**
	 * @var Commit
	 */
	protected $head;

	/**
	 * @var Entity
	 */
	protected $organization;

	/**
	 * @var Entity
	 */
	protected $sender;

	/**
	 * @var string
	 */
	protected $ref;

	/**
	 * @var Repository
	 */
	protected $repository;

	/**
	 * @var PluginInterface[]
	 */
	protected $plugins = array();

	/**
	 * @var Response
	 */
	protected $response;

	/**
	 * @param string $jsonData
	 * @param string $secret
	 */
	public function __construct($jsonData, $secret, $settingsFile = 'settings/SelfUpdate.yml') {
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
			foreach ($lister->getPluginClassNames() as $class) {
				$plugins[] = $this->loadPluginInstance(
					$class,
					(array) (TRUE === isset($settings[$package][$class]) ? $settings[$package][$class] : array())
				);
			}
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
			$file = TRUE === file_exists($file) ? $file : NULL;
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
		$errors = array();
		foreach ($this->plugins as $plugin) {
			if (TRUE === $plugin->trigger($this)) {
				try {
					$plugin->process($this);
				} catch (\RuntimeException $error) {
					$errors[] = $error;
				}
			}
		}
		if (0 < count($errors)) {
			$this->response->setCode(1);
			$this->response->setErrors($errors);
		}
		return $this->response;
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

}
