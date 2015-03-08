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

/**
 * Class Repository
 */
class Repository extends JsonDataMapper {

	const API_URL_URL = 'url';
	const API_URL_FORKS = 'forks_url';
	const API_URL_KEYS = 'keys_url';
	const API_URL_COLLABORATORS = 'collaborators_url';
	const API_URL_TEAMS = 'teams_url';
	const API_URL_HOOKS = 'hooks_url';
	const API_URL_ISSUE_EVENTS = 'issue_events_url';
	const API_URL_EVENTS = 'events_url';
	const API_URL_ASSIGNEES = 'assignees_url';
	const API_URL_BRANCHES = 'branches_url';
	const API_URL_TAGS = 'tags_url';
	const API_URL_BLOBS = 'blobs_url';
	const API_URL_GIT_TAGS = 'git_tags_url';
	const API_URL_GIT_REFS = 'git_refs_url';
	const API_URL_TREES = 'trees_url';
	const API_URL_STATUSES = 'statuses_url';
	const API_URL_LANGUAGES = 'languages_url';
	const API_URL_STARGAZERS = 'stargazers_url';
	const API_URL_CONTRIBUTORS = 'contributors_url';
	const API_URL_SUBSCRIBERS = 'subscribers_url';
	const API_URL_SUBSCRIPTION = 'subscription_url';
	const API_URL_COMMITS = 'commits_url';
	const API_URL_GIT_COMMITS = 'git_commits_url';
	const API_URL_COMMENTS = 'comments_url';
	const API_URL_ISSUE_COMMENT = 'issue_comment_url';
	const API_URL_CONTENTS = 'contents_url';
	const API_URL_COMPARE = 'compare_url';
	const API_URL_MERGES = 'merges_url';
	const API_URL_ARCHIVE = 'archive_url';
	const API_URL_DOWNLOADS = 'downloads_url';
	const API_URL_ISSUES = 'issues_url';
	const API_URL_PULLS = 'pulls_url';
	const API_URL_MILESTONES = 'milestones_url';
	const API_URL_NOTIFICATIONS = 'notifications_url';
	const API_URL_LABELS = 'labels_url';
	const API_URL_RELEASES = 'releases_url';

	/**
	 * @var array
	 */
	protected $propertyMap = array(
		'created_at' => 'created',
		'pushed_at' => 'created',
		'full_name' => 'fullName',
		'forks_count' => 'forks',
		'stargazers_count' => 'stargazers',
		'watchers_count' => 'watchers',
		'default_branch' => 'masterBranch',
		'open_issues_count' => 'openIssues',
		'has_issues' => 'hasIssues',
		'has_downloads' => 'hasDownloads',
		'has_wiki' => 'hasWiki',
		'has_pages' => 'hasPages',
		'clone_url' => 'cloneUrl',
		'git_url' => 'gitUrl',
		'ssh_url' => 'sshUrl',
	);

	/**
	 * @var array
	 */
	protected $propertyClasses = array(
		'owner' => 'NamelessCoder\\Gizzle\\Entity',
		'created' => 'DateTime',
		'pushed' => 'DateTime'
	);

	/**
	 * @var \DateTime
	 */
	protected $created = NULL;

	/**
	 * @var string
	 */
	protected $description = NULL;

	/**
	 * @var boolean
	 */
	protected $fork = NULL;

	/**
	 * @var integer
	 */
	protected $forks = NULL;

	/**
	 * @var string
	 */
	protected $fullName = NULL;

	/**
	 * @var boolean
	 */
	protected $hasDownloads = FALSE;

	/**
	 * @var boolean
	 */
	protected $hasIssues = FALSE;

	/**
	 * @var boolean
	 */
	protected $hasWiki = FALSE;

	/**
	 * @var boolean
	 */
	protected $hasPages = FALSE;

	/**
	 * @var string
	 */
	protected $homepage = NULL;

	/**
	 * @var integer
	 */
	protected $id = NULL;

	/**
	 * @var string
	 */
	protected $language = NULL;

	/**
	 * @var string
	 */
	protected $masterBranch = NULL;

	/**
	 * @var string
	 */
	protected $name = NULL;

	/**
	 * @var integer
	 */
	protected $openIssues = NULL;

	/**
	 * @var boolean
	 */
	protected $private = FALSE;

	/**
	 * @var \DateTime
	 */
	protected $pushed = NULL;

	/**
	 * @var integer
	 */
	protected $size = NULL;

	/**
	 * @var integer
	 */
	protected $stargazers = NULL;

	/**
	 * @var string
	 */
	protected $url = NULL;

	/**
	 * @var string
	 */
	protected $cloneUrl = NULL;

	/**
	 * @var string
	 */
	protected $gitUrl = NULL;

	/**
	 * @var string
	 */
	protected $sshUrl = NULL;

	/**
	 * @var integer
	 */
	protected $watchers = NULL;

	/**
	 * @var Entity
	 */
	protected $owner = NULL;

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
	 * @param string $fullName
	 */
	public function setFullName($fullName) {
		$this->fullName = $fullName;
	}

	/**
	 * @return string
	 */
	public function getFullName() {
		return $this->fullName;
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
	 * @param boolean $hasPages
	 */
	public function setHasPages($hasPages) {
		$this->hasPages = $hasPages;
	}

	/**
	 * @return boolean
	 */
	public function getHasPages() {
		return $this->hasPages;
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
	 * @param \DateTime $pushed
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
	 * @param string $cloneUrl
	 */
	public function setCloneUrl($cloneUrl) {
		$this->cloneUrl = $cloneUrl;
	}

	/**
	 * @return string
	 */
	public function getCloneUrl() {
		return $this->cloneUrl;
	}

	/**
	 * @param string $gitUrl
	 */
	public function setGitUrl($gitUrl) {
		$this->gitUrl = $gitUrl;
	}

	/**
	 * @return string
	 */
	public function getGitUrl() {
		return $this->gitUrl;
	}

	/**
	 * @param string $sshUrl
	 */
	public function setSshUrl($sshUrl) {
		$this->sshUrl = $sshUrl;
	}

	/**
	 * @return string
	 */
	public function getSshUrl() {
		return $this->sshUrl;
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
