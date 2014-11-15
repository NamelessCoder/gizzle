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
 * Class Commit
 */
class Commit extends JsonDataMapper {

	/**
	 * @var array
	 */
	protected $propertyMap = array(
		'repo' => 'repository',
		'sha' => 'sha1'
	);

	/**
	 * @var array
	 */
	protected $propertyClasses = array(
		'parents' => 'NamelessCoder\\Gizzle\\Commit[]',
		'committer' => 'NamelessCoder\\Gizzle\\Entity',
		'author' => 'NamelessCoder\\Gizzle\\Entity',
		'timestamp ' => 'DateTime',
		'repository' => 'NamelessCoder\\Gizzle\\Repository'
	);

	/**
	 * @var array
	 */
	protected $added = array();

	/**
	 * @var Entity
	 */
	protected $author = NULL;

	/**
	 * @var Entity
	 */
	protected $committer = NULL;

	/**
	 * @var boolean
	 */
	protected $distinct = FALSE;

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var Commit[]
	 */
	protected $parents = array();

	/**
	 * @var string
	 */
	protected $message = NULL;

	/**
	 * @var array
	 */
	protected $modified = array();

	/**
	 * @var array
	 */
	protected $removed = array();

	/**
	 * @var string
	 */
	protected $sha1 = NULL;

	/**
	 * @var \DateTime
	 */
	protected $timestamp = NULL;

	/**
	 * @var string
	 */
	protected $url = NULL;

	/**
	 * @var string
	 */
	protected $ref = NULL;

	/**
	 * @var Repository
	 */
	protected $repository = NULL;

	/**
	 * @param array $added
	 */
	public function setAdded($added) {
		$this->added = $added;
	}

	/**
	 * @return array
	 */
	public function getAdded() {
		return $this->added;
	}

	/**
	 * @param Entity $author
	 */
	public function setAuthor(Entity $author) {
		$this->author = $author;
	}

	/**
	 * @return Person
	 */
	public function getAuthor() {
		return $this->author;
	}

	/**
	 * @param Entity $committer
	 */
	public function setCommitter(Entity $committer) {
		$this->committer = $committer;
	}

	/**
	 * @return Entity
	 */
	public function getCommitter() {
		return $this->committer;
	}

	/**
	 * @param boolean $distinct
	 */
	public function setDistinct($distinct) {
		$this->distinct = $distinct;
	}

	/**
	 * @return boolean
	 */
	public function getDistinct() {
		return $this->distinct;
	}

	/**
	 * @param string $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param array $parents
	 */
	public function setParents(array $parents) {
		$this->parents = $parents;
	}

	/**
	 * @return array
	 */
	public function getParents() {
		return $this->parents;
	}

	/**
	 * @param string $message
	 */
	public function setMessage($message) {
		$this->message = $message;
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @param array $modified
	 */
	public function setModified(array $modified) {
		$this->modified = $modified;
	}

	/**
	 * @return array
	 */
	public function getModified() {
		return $this->modified;
	}

	/**
	 * @param array $removed
	 */
	public function setRemoved(array $removed) {
		$this->removed = $removed;
	}

	/**
	 * @return array
	 */
	public function getRemoved() {
		return $this->removed;
	}

	/**
	 * @param string $sha1
	 */
	public function setSha1($sha1) {
		$this->sha1 = $sha1;
	}

	/**
	 * @return string
	 */
	public function getSha1() {
		return $this->sha1;
	}

	/**
	 * @param \DateTime $timestamp
	 */
	public function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
	}

	/**
	 * @return \DateTime
	 */
	public function getTimestamp() {
		return $this->timestamp;
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
	 * @return Repository
	 */
	public function getRepository() {
		return $this->repository;
	}

	/**
	 * @param Repository $repository
	 * @return void
	 */
	public function setRepository(Repository $repository) {
		$this->repository = $repository;
	}

	/**
	 * @return string
	 */
	public function getRef() {
		return $this->ref;
	}

	/**
	 * @param string $ref
	 * @return void
	 */
	public function setRef($ref) {
		$this->ref = $ref;
	}

}
