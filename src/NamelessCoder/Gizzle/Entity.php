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
 * Class Entity
 */
class Entity extends JsonDataMapper {

	/**
	 * @var array
	 */
	protected $propertyMap = array(
		'login' => 'username'
	);

	/**
	 * @var string
	 */
	protected $avatar = NULL;

	/**
	 * @var string
	 */
	protected $name = NULL;

	/**
	 * @var string
	 */
	protected $email = NULL;

	/**
	 * @var string
	 */
	protected $username = NULL;

	/**
	 * @var string
	 */
	protected $url = NULL;

	/**
	 * @param string $avatar
	 */
	public function setAvatar($avatar) {
		$this->avatar = $avatar;
	}

	/**
	 * @return string
	 */
	public function getAvatar() {
		return $this->avatar;
	}

	/**
	 * @param string $email
	 * @return void
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
	 * @return void
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
	 * @return void
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

	/**
	 * @param string $url
	 * @return void
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
