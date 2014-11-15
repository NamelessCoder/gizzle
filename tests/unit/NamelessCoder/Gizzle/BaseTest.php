<?php

/**
 * This file belongs to the NamelessCoder/Gizzle package
 *
 * Copyright (c) 2014, Claus Due
 *
 * Released under the MIT license, of which the full text
 * was distributed with this package in file LICENSE.txt
 */

namespace NamelessCoder\Gizzle\Tests\Unit;

use NamelessCoder\Gizzle\Base;

/**
 * Class PayloadTest
 */
class BaseTest extends \PHPUnit_Framework_TestCase {

	protected function setUpConstant() {
		if (FALSE === defined('GIZZLE_HOME')) {
			define('GIZZLE_HOME', __DIR__);
		}
	}

	/**
	 * @dataProvider getPropertyValueDataSets
	 * @param string $property
	 * @param mixed $value
	 */
	public function testGetterAndSetter($property, $value) {
		$base = new Base();
		$getter = 'get' . ucfirst($property);
		$setter = 'set' . ucfirst($property);
		$base->$setter($value);
		$this->assertEquals($value, $base->$getter());
	}

	/**
	 * @return array
	 */
	public function getPropertyValueDataSets() {
		return array(
			array('label', 'label'),
			array('ref', 'ref')
		);
	}

}
