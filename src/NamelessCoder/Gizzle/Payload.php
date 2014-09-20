<?php
namespace NamelessCoder\Gizzle;

/**
 * Class Payload
 */
class Payload extends JsonDataMapper {

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
		'commits' => 'NamelessCoder\\Gizzle\\Commit[]',
		'head' => 'NamelessCoder\\Gizzle\\Commit',
		'sender' => 'NamelessCoder\\Gizzle\\Entity',
		'repository' => 'NamelessCoder\\Gizzle\\Repository'
	);

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
	 * @param string $jsonData
	 * @param string $secret
	 */
	public function __construct($jsonData, $secret) {
		$this->validate($jsonData, $secret);
		parent::__construct($jsonData);
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
			if (TRUE === is_array($possiblePackage)) {
				array_walk($possiblePackage, array($this, 'loadPluginsFromPackage'));
			} else {
				$this->plugins += $this->loadPluginsFromPackage($possiblePackage);
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
		$expectedListerClassName = '\\' . $package . '\\GizzlePlugins\\PluginList';
		if (TRUE === class_exists($expectedListerClassName)) {
			/** @var PluginListInterface $lister */
			$lister = new $expectedListerClassName();
			foreach ($lister->getPluginClassNames() as $pluginClassName) {
				$plugins[] = new $pluginClassName();
			}
		}
		return $plugins;
	}

	/**
	 * @return Response
	 */
	public function process() {
		$errors = array();
		$response = new Response();
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
			$response->setCode(1);
			$response->setErrors($errors);
		}
		return $response;
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

}
