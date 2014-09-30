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

use NamelessCoder\Gizzle\PluginInterface;

/**
 * Class AbstractPlugin
 */
class AbstractPlugin implements PluginInterface {

	const OPTION_EVENTS_ONSTART = 'onStart';
	const OPTION_EVENTS_ONSUCCESS = 'onSuccess';
	const OPTION_EVENTS_ONERROR = 'onError';

	/**
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Initialize the plugin with an array of settings.
	 *
	 * @param array $settings
	 * @return void
	 */
	public function initialize(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * Analyse $payload and return TRUE if this plugin should
	 * be triggered in processing the payload.
	 *
	 * @param Payload $payload
	 * @return boolean
	 */
	public function trigger(Payload $payload) {
		return TRUE;
	}

	/**
	 * Perform whichever task the Plugin should perform based
	 * on the payload's data.
	 *
	 * @param Payload $payload
	 * @return void
	 */
	public function process(Payload $payload) {
	}

	/**
	 * Returns a setting value.
	 *
	 * @return mixed
	 */
	public function getSetting($name) {
		return $this->getSettingValue($name);
	}

	/**
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	protected function getSettingValue($name, $defaultValue = NULL) {
		return TRUE === isset($this->settings[$name]) ? $this->settings[$name] : $defaultValue;
	}

}
