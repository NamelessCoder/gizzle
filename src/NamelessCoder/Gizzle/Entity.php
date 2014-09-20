<?php
namespace NamelessCoder\Gizzle;

/**
 * Class Entity
 */
class Entity extends JsonDataMapper {

	/**
	 * @var string
	 */
	private $name = NULL;

	/**
	 * @var string
	 */
	private $email = NULL;

	/**
	 * @var string
	 */
	private $username = NULL;

	/**
	 * @param string $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
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
	 * @param string $username
	 */
	public function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

}
