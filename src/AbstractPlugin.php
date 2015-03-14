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
 * Class AbstractPlugin
 */
class AbstractPlugin implements PluginInterface {

	const OPTION_EVENTS_ONSTART = 'onStart';
	const OPTION_EVENTS_ONSUCCESS = 'onSuccess';
	const OPTION_EVENTS_ONERROR = 'onError';
	const OPTION_EVENTS_ONFINISH = 'onFinish';

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
	 * @param string $name
	 * @return mixed
	 */
	public function getSetting($name) {
		return $this->getSettingValue($name);
	}

	/**
	 * Get the value of a setting or if setting does not exist,
	 * return the value of the $defaultValue parameter.
	 *
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	protected function getSettingValue($name, $defaultValue = NULL) {
		return TRUE === isset($this->settings[$name]) ? $this->settings[$name] : $defaultValue;
	}

	/**
	 * Get settings defined for a sub-plugin of this plugin.
	 * If your plugin uses other plugins internally to solve the
	 * task, then the configuration file can contain overrides
	 * for settings of each plugin by adding a sub-array indexed
	 * by the class name of the plugin that gets used.
	 *
	 * @param $pluginClassName
	 * @param array $defaults
	 * @return array
	 */
	protected function getSubPluginSettings($pluginClassName, array $defaults) {
		$settings = $this->getSettingValue($pluginClassName, $defaults);
		$settings = $this->mergeArraysRecursive($defaults, $settings);
		return $settings;
	}

	/**
	 * @param array $array1
	 * @param array $array2
	 * @return array
	 */
	protected function mergeArraysRecursive(array $array1, array $array2) {
		$merged = $array1;
		foreach ($array2 as $key => $value) {
			if (TRUE === is_array($value) && TRUE === isset($array1[$key])) {
				$value = $this->mergeArraysRecursive($array1[$key], $value);
			}
			$merged[$key] = $value;
		}
		return $merged;
	}

}
