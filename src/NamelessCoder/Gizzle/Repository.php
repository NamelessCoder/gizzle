<?php
namespace NamelessCoder\Gizzle;

/**
 * Class Repository
 */
class Repository extends JsonDataMapper {

	/**
	 * @var \DateTime
	 */
	private $created = NULL;

	/**
	 * @var string
	 */
	private $description = NULL;

	/**
	 * @var boolean
	 */
	private $fork = NULL;

	/**
	 * @var integer
	 */
	private $forks = NULL;

	/**
	 * @var boolean
	 */
	private $hasDownloads = FALSE;

	/**
	 * @var boolean
	 */
	private $hasIssues = FALSE;

	/**
	 * @var boolean
	 */
	private $hasWiki = FALSE;

	/**
	 * @var string
	 */
	private $homepage = NULL;

	/**
	 * @var integer
	 */
	private $id = NULL;

	/**
	 * @var string
	 */
	private $language = NULL;

	/**
	 * @var string
	 */
	private $masterBranch = NULL;

	/**
	 * @var string
	 */
	private $name = NULL;

	/**
	 * @var integer
	 */
	private $openIssues = NULL;

	/**
	 * @var boolean
	 */
	private $private = FALSE;

	/**
	 * @var \DateTime
	 */
	private $pushedAt = NULL;

	/**
	 * @var integer
	 */
	private $size = NULL;

	/**
	 * @var integer
	 */
	private $stargazers = NULL;

	/**
	 * @var string
	 */
	private $url = NULL;

	/**
	 * @var integer
	 */
	private $watchers = NULL;

	/**
	 * @var Entity
	 */
	private $owner = NULL;

	/**
	 * @param \DateTime $created
	 */
	public function setCreated(\DateTime $created) {
		$this->created = $created;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param integer $forks
	 */
	public function setForks($forks) {
		$this->forks = $forks;
	}

	/**
	 * @return integer
	 */
	public function getForks() {
		return $this->forks;
	}

	/**
	 * @param boolean $fork
	 */
	public function setFork($fork) {
		$this->fork = $fork;
	}
	/**
	 * @return boolean
	 */
	public function getFork() {
		return $this->fork;
	}

	/**
	 * @param boolean $hasDownloads
	 */
	public function setHasDownloads($hasDownloads) {
		$this->hasDownloads = $hasDownloads;
	}

	/**
	 * @return boolean
	 */
	public function getHasDownloads() {
		return $this->hasDownloads;
	}

	/**
	 * @param boolean $hasIssues
	 */
	public function setHasIssues($hasIssues) {
		$this->hasIssues = $hasIssues;
	}

	/**
	 * @return boolean
	 */
	public function getHasIssues() {
		return $this->hasIssues;
	}

	/**
	 * @param boolean $hasWiki
	 */
	public function setHasWiki($hasWiki) {
		$this->hasWiki = $hasWiki;
	}

	/**
	 * @return boolean
	 */
	public function getHasWiki() {
		return $this->hasWiki;
	}

	/**
	 * @param string $homepage
	 */
	public function setHomepage($homepage) {
		$this->homepage = $homepage;
	}

	/**
	 * @return string
	 */
	public function getHomepage() {
		return $this->homepage;
	}

	/**
	 * @param integer $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $language
	 */
	public function setLanguage($language) {
		$this->language = $language;
	}

	/**
	 * @return string
	 */
	public function getLanguage() {
		return $this->language;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $masterBranch
	 */
	public function setMasterBranch($masterBranch) {
		$this->masterBranch = $masterBranch;
	}

	/**
	 * @return string
	 */
	public function getMasterBranch() {
		return $this->masterBranch;
	}

	/**
	 * @param integer $openIssues
	 */
	public function setOpenIssues($openIssues) {
		$this->openIssues = $openIssues;
	}

	/**
	 * @return integer
	 */
	public function getOpenIssues() {
		return $this->openIssues;
	}

	/**
	 * @param boolean $private
	 */
	public function setPrivate($private) {
		$this->private = $private;
	}

	/**
	 * @return boolean
	 */
	public function getPrivate() {
		return $this->private;
	}

	/**
	 * @param \DateTime $pushedAt
	 */
	public function setPushed($pushed) {
		$this->pushed = $pushed;
	}

	/**
	 * @return \DateTime
	 */
	public function getPushed() {
		return $this->pushed;
	}

	/**
	 * @param integer $size
	 */
	public function setSize($size) {
		$this->size = $size;
	}

	/**
	 * @return integer
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * @param integer $stargazers
	 */
	public function setStargazers($stargazers) {
		$this->stargazers = $stargazers;
	}

	/**
	 * @return integer
	 */
	public function getStargazers() {
		return $this->stargazers;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param integer $watchers
	 */
	public function setWatchers($watchers) {
		$this->watchers = $watchers;
	}

	/**
	 * @return integer
	 */
	public function getWatchers() {
		return $this->watchers;
	}

	/**
	 * @param Entity $owner
	 */
	public function setOwner(Entity $owner) {
		$this->owner = $owner;
	}

	/**
	 * @return Entity
	 */
	public function getOwner() {
		return $this->owner;
	}

}
