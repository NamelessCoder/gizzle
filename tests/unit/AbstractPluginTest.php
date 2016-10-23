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
		$mock = $this->getMockBuilder('NamelessCoder\\Gizzle\\AbstractPlugin')->getMockForAbstractClass();
		$mock->initialize(array('foo' => 'bar'));
		$result = $this->getObjectAttribute($mock, 'settings');
		$this->assertEquals(array('foo' => 'bar'), $result);
	}

	public function testTriggerReturnsTrue() {
		$payload = $this->getMockBuilder('NamelessCoder\\Gizzle\\Payload')->disableOriginalConstructor()->getMock();
        $mock = $this->getMockBuilder('NamelessCoder\\Gizzle\\AbstractPlugin')->getMockForAbstractClass();
		$result = $mock->trigger($payload);
		$this->assertTrue($result);
	}

	public function testProcessDoesNothing() {
		$payload = $this->getMockBuilder('NamelessCoder\\Gizzle\\Payload')->disableOriginalConstructor()->setMethods(array('getResponse'))->getMock();
		$payload->expects($this->never())->method('getResponse');
        $mock = $this->getMockBuilder('NamelessCoder\\Gizzle\\AbstractPlugin')->getMockForAbstractClass();
		$result = $mock->process($payload);
		$this->assertNull($result);
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
		$mock = $this->getMockBuilder('NamelessCoder\\Gizzle\\AbstractPlugin')->disableOriginalConstructor()->setMethods(array('getSettingValue'))->getMockForAbstractClass();
		$mock->expects($this->once())->method('getSettingValue')->with('foobar');
		$mock->getSetting('foobar');
	}

	public function testGetSubPluginSettingsReturnsSettingsAndMergesDefaultsWithOverride() {
		$parameters = array('foo' => 'nuked', 'array' => array('baz' => 'baz'));
		$mock = $this->getMockBuilder('NamelessCoder\\Gizzle\\AbstractPlugin')->disableOriginalConstructor()->setMethods(array('getSettingValue'))->getMock();
		$mock->expects($this->once())->method('getSettingValue')->with('foobar', $parameters)
			->willReturn(array('bar' => 'foo', 'foo' => 'bar', 'array' => array('baz' => 'baz')));
		$method = new \ReflectionMethod('NamelessCoder\\Gizzle\\AbstractPlugin', 'getSubPluginSettings');
		$method->setAccessible(TRUE);
		$result = $method->invokeArgs($mock, array('foobar', $parameters));
		$this->assertEquals(array('foo' => 'bar', 'bar' => 'foo', 'array' => array('baz' => 'baz')), $result);
	}

}
