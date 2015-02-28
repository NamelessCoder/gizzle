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
 * Class Base
 */
class Base extends JsonDataMapper {

	/**
	 * @var string
	 */
	protected $label = NULL;

	/**
	 * @var string
	 */
	protected $ref = NULL;

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @param string $label
	 * @return void
	 */
	public function setLabel($label) {
		$this->label = $label;
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
