<?php

/**
 * This file belongs to the NamelessCoder/Gizzle package
 *
 * Copyright (c) 2014, Claus Due
 *
 * Released under the MIT license, of which the full text
 * was distributed with this package in file LICENSE.txt
 */

namespace FluidTYPO3\FluidTYPO3Gizzle\Tests\Unit\GizzlePlugins;

use NamelessCoder\Gizzle\GizzlePlugins\SelfUpdatePlugin;
use NamelessCoder\Gizzle\Payload;
use NamelessCoder\Gizzle\Response;

/**
 * Class SelfUpdatePluginTest
 */
class SelfUpdatePluginTest extends \PHPUnit_Framework_TestCase {

	public function testGetCommandReturnsExpectedCommand() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('getRef', 'getResponse'), array(), '', FALSE);
		$payload->expects($this->once())->method('getResponse')->will($this->returnValue(new Response()));
		$mock = $this->getMock('NamelessCoder\\Gizzle\\GizzlePlugins\\SelfUpdatePlugin', array('getCommand', 'invokeShellCommand'));
		$mock->expects($this->once())->method('invokeShellCommand')->with('ls', array());
		$mock->expects($this->once())->method('getCommand')->will($this->returnValue('ls'));
		$mock->process($payload);
	}

	public function testInvokeShellCommandReturnsShellReturnCodeAndSetsOutput() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('getRef', 'getResponse'), array(), '', FALSE);
		$payload->expects($this->once())->method('getResponse')->will($this->returnValue(new Response()));
		$mock = $this->getMock('NamelessCoder\\Gizzle\\GizzlePlugins\\SelfUpdatePlugin', array('getCommand'));
		$mock->expects($this->once())->method('getCommand')->will($this->returnValue('ls'));
		$mock->process($payload);
	}

	public function testProcessInvokesShellCommand() {
		$mock = $this->getMock('NamelessCoder\\Gizzle\\GizzlePlugins\\SelfUpdatePlugin', array('invokeShellCommand'));
		$mock->expects($this->once())->method('invokeShellCommand')->will($this->returnValue(0));
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('getResponse'), array(), '', FALSE);
		$payload->expects($this->once())->method('getResponse')->will($this->returnValue(new Response()));
		$mock->process($payload);
	}

	/**
	 * @dataProvider getExpectedSettingsAndTriggerResults
	 * @param string $payloadBranch
	 * @param mixed $enabledFlag
	 * @param mixed $matchingBranch
	 * @param boolean $expectation
	 */
	public function testTriggerRespectsEnabledOption($payloadBranch, $enabledFlag, $matchingBranch, $expectation) {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('getRef'), array(), '', FALSE);
		$mock = new SelfUpdatePlugin();
		$settings = array(SelfUpdatePlugin::OPTION_ENABLED => $enabledFlag);
		if (NULL !== $matchingBranch) {
			$settings[SelfUpdatePlugin::OPTION_BRANCH] = $matchingBranch;
			$payload->expects($this->once())->method('getRef')->will($this->returnValue($payloadBranch));
		}
		$mock->initialize($settings);
		$this->assertEquals($expectation, $mock->trigger($payload));
	}

	/**
	 * @return array
	 */
	public function getExpectedSettingsAndTriggerResults() {
		return array(
			array('refs/heads/master', TRUE, 'master', TRUE),
			array('refs/heads/otherbranch', TRUE, 'master', FALSE),
			array('refs/heads/master', TRUE, 'notmaster', FALSE),
			array('refs/heads/otherbranch', TRUE, 'notmaster', FALSE),
			array('', FALSE, 'master', FALSE),
			array(NULL, TRUE, NULL, TRUE),
			array(NULL, FALSE, NULL, FALSE),
		);
	}

}
