<?php

/**
 * This file belongs to the NamelessCoder/Gizzle package
 *
 * Copyright (c) 2014, Claus Due
 *
 * Released under the MIT license, of which the full text
 * was distributed with this package in file LICENSE.txt
 */

namespace NamelessCoder\Gizzle\GizzlePlugins;

use NamelessCoder\Gizzle\PluginListInterface;

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
	 * @return array
	 */
	public function getPluginClassNames() {
		return array(
			'NamelessCoder\\Gizzle\\GizzlePlugins\\SelfUpdatePlugin',
			'NamelessCoder\\Gizzle\\GizzlePlugins\\CommentPlugin',
		);
	}

}
