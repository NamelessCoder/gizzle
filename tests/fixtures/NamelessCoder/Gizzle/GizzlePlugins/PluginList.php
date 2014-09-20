<?php
namespace NamelessCoder\Gizzle\Tests\Fixtures\GizzlePlugins;

use NamelessCoder\Gizzle\PluginListInterface;

/**
 * Class PluginList
 */
class PluginList implements PluginListInterface {

	/**
	 * Get all class names of plugins delivered from implementer package.
	 *
	 * @return string[]
	 */
	public function getPluginClassNames() {
		return array(
			'NamelessCoder\\Gizzle\\Tests\\Fixtures\\GizzlePlugins\\Plugin'
		);
	}

}