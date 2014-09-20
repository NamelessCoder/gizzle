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
		'timestamp ' => 'DateTime'
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
	 * @var \DateTime
	 */
	protected $timestamp = NULL;

	/**
	 * @var string
	 */
	protected $url = NULL;

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
