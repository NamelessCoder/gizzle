<?php

/**
 * This file belongs to the NamelessCoder/Gizzle package
 *
 * Copyright (c) 2014, Claus Due
 *
 * Released under the MIT license, of which the full text
 * was distributed with this package in file LICENSE.txt
 */

namespace NamelessCoder\Gizzle\Tests\Fixtures\GizzlePlugins;

use NamelessCoder\Gizzle\AbstractPlugin;
use NamelessCoder\Gizzle\PluginInterface;

/**
 * Class AccessiblePlugin
 *
 * Fixture with public delegation of all protected methods.
 */
class AccessiblePlugin extends AbstractPlugin {

	/**
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function getSettingValue($name, $defaultValue = NULL) {
		return parent::getSettingValue($name, $defaultValue);
	}

}
