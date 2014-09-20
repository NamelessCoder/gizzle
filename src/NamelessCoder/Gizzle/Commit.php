<?php
namespace NamelessCoder\Gizzle;

/**
 * Class Commit
 */
class Commit extends JsonDataMapper {

	/**
	 * @var string[]
	 */
	private $added = array();

	/**
	 * @var Person
	 */
	private $author = null;

	/**
	 * @var Person
	 */
	private $committer = null;

	/**
	 * @var boolean
	 */
	private $distinct = false;

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $message = null;

	/**
	 * @var string[]
	 */
	private $modified = array();

	/**
	 * @var string[]
	 */
	private $removed = array();

	/**
	 * @var \DateTime
	 */
	private $timestamp = null;

	/**
	 * @var string
	 */
	private $url = null;

	/**
	 * @param string[] $added
	 */
	public function setAdded($added) {
		$this->added = $added;
	}

	/**
	 * @return string[]
	 */
	public function getAdded() {
		return $this->added;
	}

	/**
	 * @param Person $author
	 */
	public function setAuthor(Person $author) {
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
	 * @param string[] $modified
	 */
	public function setModified(array $modified) {
		$this->modified = $modified;
	}

	/**
	 * @return string[]
	 */
	public function getModified() {
		return $this->modified;
	}

	/**
	 * @param string[] $removed
	 */
	public function setRemoved(array $removed) {
		$this->removed = $removed;
	}

	/**
	 * @return string[]
	 */
	public function getRemoved() {
		return $this->removed;
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

}
