<?php
namespace NamelessCoder\Gizzle;

/**
 * Class Commit
 */
class Commit extends JsonDataMapper {

	/**
	 * @var array
	 */
	protected $propertyClasses = array(
		'committer' => 'NamelessCoder\\Gizzle\\Entity',
		'author' => 'NamelessCoder\\Gizzle\\Entity',
	);

	/**
	 * @var string[]
	 */
	protected $added = array();

	/**
	 * @var Entity
	 */
	protected $author = null;

	/**
	 * @var Entity
	 */
	protected $committer = null;

	/**
	 * @var boolean
	 */
	protected $distinct = false;

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $message = null;

	/**
	 * @var string[]
	 */
	protected $modified = array();

	/**
	 * @var string[]
	 */
	protected $removed = array();

	/**
	 * @var \DateTime
	 */
	protected $timestamp = null;

	/**
	 * @var string
	 */
	protected $url = null;

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
