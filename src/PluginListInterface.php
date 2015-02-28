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
 * Interface PluginListInterface
 */
interface PluginListInterface {

	/**
	 * Initialize the plugin lister with an array of settings.
	 *
	 * @param array $settings
	 * @return void
	 */
	public function initialize(array $settings);

	/**
	 * Get all class names of plugins delivered from implementer package.
	 *
	 * @return string[]
	 */
	public function getPluginClassNames();

}