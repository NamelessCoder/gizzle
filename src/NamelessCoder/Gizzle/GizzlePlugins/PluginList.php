<?php
namespace NamelessCoder\Gizzle\GizzlePlugins;

use NamelessCoder\Gizzle\PluginListInterface;
use string;

/**
 * Class PluginList
 */
class PluginList implements PluginListInterface {

	/**
	 * @var
	 */
	protected $settings;

	/**
	 * Initialize the plugin lister with an array of settings.
	 *
	 * @param array $settings
	 */
	public function initialize(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * Get all class names of plugins delivered from implementer package.
	 *
	 * @return string[]
	 */
	public function getPluginClassNames() {
		return array(
			'NamelessCoder\\Gizzle\\GizzlePlugins\\SelfUpdatePlugin'
		);
	}

}
