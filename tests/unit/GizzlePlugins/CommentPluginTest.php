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

use Milo\Github\Api;
use NamelessCoder\Gizzle\Commit;
use NamelessCoder\Gizzle\GizzlePlugins\CommentPlugin;
use NamelessCoder\Gizzle\Payload;
use NamelessCoder\Gizzle\PullRequest;
use NamelessCoder\Gizzle\Response;

/**
 * Class CommentPluginTest
 */
class CommentPluginTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider getExpectedSettingsAndTriggerResults
	 * @param mixed $enabledFlag
	 * @param mixed $api
	 * @param boolean $expectation
	 */
	public function testTriggerRespectsEnabledOption($enabledFlag, $api, $expectation) {
		$payload = $this->getMockBuilder('NamelessCoder\\Gizzle\\Payload')->setMethods(array('getApi'))->disableOriginalConstructor()->getMock();
		$payload->expects($this->once())->method('getApi')->willReturn($api);
		$mock = new CommentPlugin();
		$settings = array(CommentPlugin::OPTION_ENABLED => $enabledFlag);
		$mock->initialize($settings);
		$this->assertEquals($expectation, $mock->trigger($payload));
	}

	/**
	 * @return array
	 */
	public function getExpectedSettingsAndTriggerResults() {
		$api = new Api();
		return array(
			array(TRUE, NULL, FALSE),
			array(FALSE, NULL, FALSE),
			array(FALSE, $api, FALSE),
			array(TRUE, $api, TRUE),
		);
	}

	/**
	 * @dataProvider getProcessTestValues
	 * @param CommentPlugin $plugin
	 * @param array $settings
	 * @param array $errors
	 * @param array $output
	 * @param string|NULL $expectedMethod
	 * @param string $expectedComment
	 */
	public function testProcess(CommentPlugin $plugin, array $settings, array $errors, array $output, $expectedMethod, $expectedComment) {
		$payload = $this->getMockBuilder('NamelessCoder\\Gizzle\\Payload')->setMethods(array($expectedMethod))->disableOriginalConstructor()->getMock();
		$propertyReflection = new \ReflectionProperty($payload, 'response');
		$propertyReflection->setAccessible(TRUE);
		$propertyReflection->setValue($payload, new Response());
		$pullRequest = new PullRequest();
		$pullRequest->setId('pull-request-id');
		$commit = new Commit();
		$commit->setId('commit-id');
		$plugin->initialize($settings);
		$payload->setPullRequest($pullRequest);
		$payload->setHead($commit);
		if (0 < count($errors)) {
			$payload->getResponse()->setErrors($errors);
		}
		if (0 < count($output)) {
			$payload->getResponse()->addOutputFromPlugin($plugin, $output);
		}
		if ('dummy' !== $expectedMethod) {
			$payload->expects($this->once())->method($expectedMethod)->with($this->anything(), $expectedComment);
		}
		$plugin->process($payload);
	}

	/**
	 * @return array
	 */
	public function getProcessTestValues() {
		$plugin = new CommentPlugin();
		$pluginClass = get_class($plugin) . ':' . spl_object_hash($plugin);
		return array(
			array($plugin, array(), array(), array(), 'dummy', NULL),
			array($plugin, array('commit' => TRUE),
				array(),
				array('foo' => 'bar'),
				'storeCommitComment',
				'- ' . $pluginClass . ':' . PHP_EOL . '  - foo: bar'
			),
			array($plugin, array('commit' => TRUE),
				array(),
				array('foo' => array('bar' => 'baz')),
				'storeCommitComment',
				'- ' . $pluginClass . ':' . PHP_EOL . '  - foo:' . PHP_EOL . '    - bar: baz'
			),
			array($plugin, array('pullRequest' => TRUE),
				array(),
				array('baz' => 'foo'),
				'storePullRequestComment',
				'- ' . $pluginClass . ':' . PHP_EOL . '  - baz: foo'
			),
			array($plugin, array('commit' => TRUE, 'comment' => 'abc'),
				array(),
				array('foo' => 'bar'),
				'storeCommitComment',
				'abc' . PHP_EOL . PHP_EOL . '- ' . $pluginClass . ':' . PHP_EOL . '  - foo: bar'
			),
			array($plugin, array('commit' => TRUE),
				array(new \RuntimeException('test', 123)),
				array(),
				'storeCommitComment',
				'- Error code: 123 - test'
			),
		);
	}

}
