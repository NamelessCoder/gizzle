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

use NamelessCoder\Gizzle\Tests\Fixtures\GizzlePlugins\AccessiblePlugin;

/**
 * Class AbstractPluginTest
 */
class AbstractPluginTest extends \PHPUnit_Framework_TestCase {

	public function testInitializeSetsSettings() {
		$mock = $this->getMockForAbstractClass('NamelessCoder\\Gizzle\\AbstractPlugin');
		$mock->initialize(array('foo' => 'bar'));
		$result = $this->getObjectAttribute($mock, 'settings');
		$this->assertEquals(array('foo' => 'bar'), $result);
	}

	public function testTriggerReturnsTrue() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array(), array(), '', FALSE);
		$mock = $this->getMockForAbstractClass('NamelessCoder\\Gizzle\\AbstractPlugin');
		$result = $mock->trigger($payload);
		$this->assertTrue($result);
	}

	public function testProcessDoesNothing() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('getResponse'), array(), '', FALSE);
		$payload->expects($this->never())->method('getResponse');
		$mock = $this->getMockForAbstractClass('NamelessCoder\\Gizzle\\AbstractPlugin');
		$result = $mock->process($payload);
	}

	public function testGetSettingValueReturnsDefaultIfValueNotSet() {
		$mock = new AccessiblePlugin();
		$result = $mock->getSettingValue('doesnotexist', 'expected');
		$this->assertEquals('expected', $result);
	}

	public function testGetSettingValueReturnsValueIfSet() {
		$mock = new AccessiblePlugin();
		$mock->initialize(array('exists' => 'expected'));
		$result = $mock->getSettingValue('exists', NULL);
		$this->assertEquals('expected', $result);
	}

	public function testGetSettingDelegatesToGetSettingValue() {
		$mock = $this->getMockForAbstractClass('NamelessCoder\\Gizzle\\AbstractPlugin', array(), '', FALSE, FALSE, TRUE, array('getSettingValue'));
		$mock->expects($this->once())->method('getSettingValue')->with('foobar');
		$mock->getSetting('foobar');
	}

}