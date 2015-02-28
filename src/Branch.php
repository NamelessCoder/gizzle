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
 * Class Branch
 */
class Branch extends JsonDataMapper {

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var Commit
	 */
	protected $commit;

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
	 * @param Commit $commit
	 * @return void
	 */
	public function setCommit(Commit $commit) {
		$this->commit = $commit;
	}

	/**
	 * @return Commit
	 */
	public function getCommit() {
		return $this->commit;
	}

}
