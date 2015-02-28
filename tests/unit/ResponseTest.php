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

use NamelessCoder\Gizzle\Response;

/**
 * Class ResponseTest
 */
class ResponseTest extends \PHPUnit_Framework_TestCase {

	public function addOutputFromPluginStoresOutput() {
		$response = new Response();
		$plugin = $this->getMockForAbstractClass('NamelessCoder\\Gizzle\\PluginInterface');
		$hash = get_class($plugin) . ':' . spl_object_hash($plugin);
		$expected = array($hash => array('foo' => 'bar'));
		$response->addOutputFromPlugin($plugin, array('foo' => 'bar'));
		$this->assertEquals($expected, $response->getOutput());
	}

	public function testGetOutputReturnsArray() {
		$response = new Response();
		$result = $response->getOutput();
		$this->assertEquals(array(), $result);
	}

	/**
	 * @dataProvider getPropertyValueDataSets
	 * @param string $property
	 * @param mixed $value
	 */
	public function testGetterAndSetter($property, $value) {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Response', array('__construct'), array('{}', ''));
		$getter = 'get' . ucfirst($property);
		$setter = 'set' . ucfirst($property);
		$payload->$setter($value);
		$this->assertEquals($value, $payload->$getter());
	}

	/**
	 * @return array
	 */
	public function getPropertyValueDataSets() {
		return array(
			array('code', 1),
			array('errors', array(new \RuntimeException(), new \RuntimeException())),
		);
	}

}