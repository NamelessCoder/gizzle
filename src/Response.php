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
 * Class Response
 */
class Response {

	const OK = 0;
	const ERROR = 1;

	/**
	 * @var array
	 */
	protected $output = array();

	/**
	 * @var \RuntimeException[]
	 */
	protected $errors = array();

	/**
	 * @var integer
	 */
	protected $code = self::OK;

	/**
	 * @param \RuntimeException[] $errors
	 * @return $this
	 */
	public function setErrors(array $errors) {
		$this->errors = $errors;
		$this->code = self::ERROR;
		return $this;
	}

	/**
	 * @return \Exception[]
	 */
	public function getErrors() {
		return $this->errors;
	}

	/**
	 * @param integer $code
	 * @return $this
	 */
	public function setCode($code) {
		$this->code = $code;
		return $this;
	}

	/**
	 * @return integer
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * @return array
	 */
	public function getOutput() {
		return $this->output;
	}

	/**
	 * @param PluginInterface $plugin
	 * @param array $output
	 */
	public function addOutputFromPlugin(PluginInterface $plugin, array $output) {
		$pluginClass = get_class($plugin) . ':' . spl_object_hash($plugin);
		if (FALSE === isset($this->output[$pluginClass])) {
			$this->output[$pluginClass] = array();
		}
		$this->output[$pluginClass] = array_merge($this->output[$pluginClass], $output);
	}

	/**
	 * @param \RuntimeException $error
	 * @return $this
	 */
	public function addError(\RuntimeException $error) {
		$this->errors[] = $error;
		$this->code = self::OK === $this->code ? self::ERROR : self::OK;
		return $this;
	}

}
