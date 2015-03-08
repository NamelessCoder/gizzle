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
 * Class PullRequest
 */
class PullRequest extends JsonDataMapper {

	const API_URL_HTML = 'html_url';
	const API_URL_DIFF = 'diff_url';
	const API_URL_PATCH = 'patch_url';
	const API_URL_ISSUE = 'issue_url';
	const API_URL_COMMITS = 'commits_url';
	const API_URL_COMMENTS = 'comments_url';
	const API_URL_STATUSES = 'statuses_url';
	const API_URL_REVIEW_COMMENTS = 'review_comments_URL';

	/**
	 * @var array
	 */
	protected $propertyMap = array(
		'created_at' => 'dateCreated',
		'updated_at' => 'dateUpdated',
		'closed_at' => 'dateClosed',
		'merged_at' => 'dateMerged',
		'merge_commit_sha' => 'mergeCommitSha',
		'mergeable_state' => 'mergeableState',
		'merged_by' => 'mergedBy',
		'review_comments' => 'reviewComments',
		'changed_files' => 'changedFiles',
	);

	/**
	 * @var array
	 */
	protected $propertyClasses = array(
		'head' => 'NamelessCoder\\Gizzle\\Commit',
		'base' => 'NamelessCoder\\Gizzle\\Base',
		'user' => 'NamelessCoder\\Gizzle\\Entity',
		'assignee' => 'NamelessCoder\\Gizzle\\Entity',
		'mergedBy' => 'NamelessCoder\\Gizzle\\Entity',
		'created' => 'DateTime',
		'updated' => 'DateTime',
		'closed' => 'DateTime',
		'merged' => 'DateTime'
	);

	/**
	 * @var Commit
	 */
	protected $head = NULL;

	/**
	 * @var Base
	 */
	protected $base = NULL;

	/**
	 * @var string
	 */
	protected $milestone = NULL;

	/**
	 * @var string
	 */
	protected $id = NULL;

	/**
	 * @var string
	 */
	protected $number = NULL;

	/**
	 * @var \DateTime
	 */
	protected $dateCreated = NULL;

	/**
	 * @var \DateTime
	 */
	protected $dateUpdated = NULL;

	/**
	 * @var \DateTime
	 */
	protected $dateClosed = NULL;

	/**
	 * @var string
	 */
	protected $state = NULL;

	/**
	 * @var boolean
	 */
	protected $locked = FALSE;

	/**
	 * @var boolean
	 */
	protected $merged = FALSE;

	/**
	 * @var boolean
	 */
	protected $mergeable = FALSE;

	/**
	 * @var string
	 */
	protected $mergeableState = NULL;

	/**
	 * @var Entity
	 */
	protected $mergedBy = NULL;

	/**
	 * @var string
	 */
	protected $title = NULL;

	/**
	 * @var string
	 */
	protected $body = NULL;

	/**
	 * @var Entity
	 */
	protected $user = NULL;

	/**
	 * @var Entity
	 */
	protected $assignee = NULL;

	/**
	 * @var string
	 */
	protected $mergeCommitSha = NULL;

	/**
	 * @var integer
	 */
	protected $comments = 0;

	/**
	 * @var integer
	 */
	protected $reviewComments = 0;

	/**
	 * @var integer
	 */
	protected $commits = 0;

	/**
	 * @var integer
	 */
	protected $additions = 0;

	/**
	 * @var integer
	 */
	protected $deletions = 0;

	/**
	 * @var integer
	 */
	protected $changedFiles = 0;

	/**
	 * @return integer
	 */
	public function getAdditions() {
		return $this->additions;
	}

	/**
	 * @param integer $additions
	 * @return void
	 */
	public function setAdditions($additions) {
		$this->additions = $additions;
	}

	/**
	 * @return Entity
	 */
	public function getAssignee() {
		return $this->assignee;
	}

	/**
	 * @param Entity $assignee
	 * @return void
	 */
	public function setAssignee(Entity $assignee) {
		$this->assignee = $assignee;
	}

	/**
	 * @return Base
	 */
	public function getBase() {
		return $this->base;
	}

	/**
	 * @param Base $base
	 * @return void
	 */
	public function setBase(Base $base) {
		$this->base = $base;
	}

	/**
	 * @return string
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * @param string $body
	 * @return void
	 */
	public function setBody($body) {
		$this->body = $body;
	}

	/**
	 * @return integer
	 */
	public function getChangedFiles() {
		return $this->changedFiles;
	}

	/**
	 * @param integer $changedFiles
	 * @return void
	 */
	public function setChangedFiles($changedFiles) {
		$this->changedFiles = $changedFiles;
	}

	/**
	 * @return integer
	 */
	public function getComments() {
		return $this->comments;
	}

	/**
	 * @param integer $comments
	 * @return void
	 */
	public function setComments($comments) {
		$this->comments = $comments;
	}

	/**
	 * @return integer
	 */
	public function getCommits() {
		return $this->commits;
	}

	/**
	 * @param integer $commits
	 * @return void
	 */
	public function setCommits($commits) {
		$this->commits = $commits;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateClosed() {
		return $this->dateClosed;
	}

	/**
	 * @param \DateTime $dateClosed
	 * @return void
	 */
	public function setDateClosed(\DateTime $dateClosed) {
		$this->dateClosed = $dateClosed;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateCreated() {
		return $this->dateCreated;
	}

	/**
	 * @param \DateTime $dateCreated
	 * @return void
	 */
	public function setDateCreated(\DateTime $dateCreated) {
		$this->dateCreated = $dateCreated;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateUpdated() {
		return $this->dateUpdated;
	}

	/**
	 * @param \DateTime $dateUpdated
	 * @return void
	 */
	public function setDateUpdated(\DateTime $dateUpdated) {
		$this->dateUpdated = $dateUpdated;
	}

	/**
	 * @return integer
	 */
	public function getDeletions() {
		return $this->deletions;
	}

	/**
	 * @param integer $deletions
	 * @return void
	 */
	public function setDeletions($deletions) {
		$this->deletions = $deletions;
	}

	/**
	 * @return Commit
	 */
	public function getHead() {
		return $this->head;
	}

	/**
	 * @param Commit $head
	 * @return void
	 */
	public function setHead(Commit $head) {
		$this->head = $head;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $id
	 * @return void
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return boolean
	 */
	public function getLocked() {
		return $this->locked;
	}

	/**
	 * @param boolean $locked
	 * @return void
	 */
	public function setLocked($locked) {
		$this->locked = $locked;
	}

	/**
	 * @return string
	 */
	public function getMergeCommitSha() {
		return $this->mergeCommitSha;
	}

	/**
	 * @param string $mergeCommitSha
	 * @return void
	 */
	public function setMergeCommitSha($mergeCommitSha) {
		$this->mergeCommitSha = $mergeCommitSha;
	}

	/**
	 * @return boolean
	 */
	public function getMergeable() {
		return $this->mergeable;
	}

	/**
	 * @param boolean $mergeable
	 * @return void
	 */
	public function setMergeable($mergeable) {
		$this->mergeable = $mergeable;
	}

	/**
	 * @return string
	 */
	public function getMergeableState() {
		return $this->mergeableState;
	}

	/**
	 * @param string $mergeableState
	 * @return void
	 */
	public function setMergeableState($mergeableState) {
		$this->mergeableState = $mergeableState;
	}

	/**
	 * @return boolean
	 */
	public function getMerged() {
		return $this->merged;
	}

	/**
	 * @param boolean $merged
	 * @return void
	 */
	public function setMerged($merged) {
		$this->merged = $merged;
	}

	/**
	 * @return Entity
	 */
	public function getMergedBy() {
		return $this->mergedBy;
	}

	/**
	 * @param Entity $mergedBy
	 * @return void
	 */
	public function setMergedBy(Entity $mergedBy) {
		$this->mergedBy = $mergedBy;
	}

	/**
	 * @return string
	 */
	public function getMilestone() {
		return $this->milestone;
	}

	/**
	 * @param string $milestone
	 * @return void
	 */
	public function setMilestone($milestone) {
		$this->milestone = $milestone;
	}

	/**
	 * @return string
	 */
	public function getNumber() {
		return $this->number;
	}

	/**
	 * @param string $number
	 * @return void
	 */
	public function setNumber($number) {
		$this->number = $number;
	}

	/**
	 * @return integer
	 */
	public function getReviewComments() {
		return $this->reviewComments;
	}

	/**
	 * @param integer $reviewComments
	 * @return void
	 */
	public function setReviewComments($reviewComments) {
		$this->reviewComments = $reviewComments;
	}

	/**
	 * @return string
	 */
	public function getState() {
		return $this->state;
	}

	/**
	 * @param string $state
	 * @return void
	 */
	public function setState($state) {
		$this->state = $state;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @return Entity
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @param Entity $user
	 * @return void
	 */
	public function setUser(Entity $user) {
		$this->user = $user;
	}

}
