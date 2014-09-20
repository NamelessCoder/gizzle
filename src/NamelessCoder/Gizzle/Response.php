<?php
namespace NamelessCoder\Gizzle;

/**
 * Class Response
 */
class Response {

	/**
	 * @var \Exception[]
	 */
	protected $errors = array();

	/**
	 * @var integer
	 */
	protected $code = 0;

	/**
	 * @param \Exception[] $errors
	 */
	public function setErrors(array $errors) {
		$this->errors = $errors;
	}

	/**
	 * @return \Exception[]
	 */
	public function getErrors() {
		return $this->errors;
	}

	/**
	 * @param integer $code
	 * @return void
	 */
	public function setCode($code) {
		$this->code = $code;
	}

	/**
	 * @return integer
	 */
	public function getCode() {
		return $this->code;
	}

}
