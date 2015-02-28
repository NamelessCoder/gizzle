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

use Milo\Github\Api;
use NamelessCoder\Gizzle\AbstractPlugin;
use NamelessCoder\Gizzle\Base;
use NamelessCoder\Gizzle\Branch;
use NamelessCoder\Gizzle\Commit;
use NamelessCoder\Gizzle\Entity;
use NamelessCoder\Gizzle\Message;
use NamelessCoder\Gizzle\Payload;
use NamelessCoder\Gizzle\PullRequest;
use NamelessCoder\Gizzle\Repository;
use NamelessCoder\Gizzle\Response;
use NamelessCoder\Gizzle\Tests\Fixtures\GizzlePlugins\AccessiblePlugin;
use NamelessCoder\Gizzle\Tests\Fixtures\GizzlePlugins\ErrorPlugin;
use NamelessCoder\Gizzle\Tests\Fixtures\GizzlePlugins\Plugin;

/**
 * Class PayloadTest
 */
class PayloadTest extends \PHPUnit_Framework_TestCase {

	protected function setUpConstant() {
		if (FALSE === defined('GIZZLE_HOME')) {
			define('GIZZLE_HOME', __DIR__);
		}
	}

	public function testConstructorThrowsRuntimeExceptionIfGizzleHomeConstantNotDefined() {
		$data = file_get_contents('tests/fixtures/sample-payload.json');
		$this->setExpectedException('RuntimeException');
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('validate'), array($data, ''));
	}

	public function testConstructorAcceptsFixtureJson() {
		$this->setUpConstant();
		$data = file_get_contents('tests/fixtures/sample-payload.json');
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('validate'), array($data, ''));
	}

	public function testReadSignatureHeader() {
		// test: execution *without* this mocked request header will fail
		$_SERVER['HTTP_X_HUB_SIGNATURE'] = 'sha1=' . hash_hmac('sha1', '{}', '');
		$instance = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('isCommandLine'), array(), '', FALSE);
		$instance->expects($this->once())->method('isCommandLine')->will($this->returnValue(FALSE));
		$instance->__construct('{}', '');
	}

	public function testDispatchPluginEventExecutesEventPlugins() {
		$plugin = new AccessiblePlugin();
		$plugin->initialize(array(
			AbstractPlugin::OPTION_EVENTS_ONSTART => array(
				get_class($plugin) => array()
			)
		));
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('loadPluginInstances', 'executePlugin'), array('{}', ''));
		$payload->expects($this->once())->method('loadPluginInstances')
			->with($plugin->getSetting(AbstractPlugin::OPTION_EVENTS_ONSTART))->will($this->returnValue(array($plugin)));
		$payload->expects($this->once())->method('executePlugin')->with($plugin);
		$method = new \ReflectionMethod($payload, 'dispatchPluginEvent');
		$method->setAccessible(TRUE);
		$method->invoke($payload, $plugin, AbstractPlugin::OPTION_EVENTS_ONSTART);
	}

	public function testDispatchPluginEventWithExceptionAddsToResponse() {
		$plugin = new AccessiblePlugin();
		$plugin->initialize(array(
			AbstractPlugin::OPTION_EVENTS_ONSTART => array(
				get_class($plugin) => array()
			)
		));
		$exception = new \RuntimeException();
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('loadPluginInstances', 'executePlugin'), array('{}', ''));
		$payload->expects($this->once())->method('loadPluginInstances')
			->with($plugin->getSetting(AbstractPlugin::OPTION_EVENTS_ONSTART))->will($this->returnValue(array($plugin)));
		$payload->expects($this->once())->method('executePlugin')->with($plugin)->will($this->throwException($exception));
		$method = new \ReflectionMethod($payload, 'dispatchPluginEvent');
		$method->setAccessible(TRUE);
		$method->invoke($payload, $plugin, AbstractPlugin::OPTION_EVENTS_ONSTART);
		$this->assertNotEmpty($payload->getResponse()->getOutput());
	}

	public function testLoadPlugins() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('validate', 'loadPluginsFromPackage'), array('{}', ''));
		$payload->expects($this->once())->method('loadPluginsFromPackage')
			->with('NamelessCoder\\Gizzle')
			->will($this->returnValue(array()));
		$payload->loadPlugins('NamelessCoder\\Gizzle');
	}

	public function testLoadPluginsSupportsArray() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('validate', 'loadPluginsFromPackage'), array('{}', ''));
		$payload->expects($this->exactly(2))->method('loadPluginsFromPackage')
			->with('NamelessCoder\\Gizzle')
			->will($this->returnValue(array()));
		$payload->loadPlugins(array('NamelessCoder\\Gizzle', 'NamelessCoder\\Gizzle'));
	}

	public function testLoadPluginsLoadsExpectedPlugins() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('validate'), array('{}', ''));
		$payload->loadPlugins('NamelessCoder\\Gizzle\\Tests\\Fixtures');
		$this->assertAttributeEquals(array(new Plugin()), 'plugins', $payload);
	}

	public function testLoadsAndMapsPayloadData() {
		$data = file_get_contents('tests/fixtures/sample-payload.json');
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('validate'), array($data, ''));
		$payload->loadPlugins('NamelessCoder\\Gizzle\\Tests\\Fixtures');
		$result = $payload->process();
		$this->assertInstanceOf('NamelessCoder\\Gizzle\\Response', $result);
		$this->assertEquals(0, $result->getCode());
	}

	public function testProcessLoadsSettingsForConfiguredPackagesIfPluginListEmpty() {
		$data = file_get_contents('tests/fixtures/sample-payload.json');
		$secret = 'dummysecret';
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('loadSettings', 'loadPlugins'), array($data, $secret), '', FALSE);
		$payload->expects($this->once())->method('loadSettings')->will($this->returnValue(array('foo' => 'bar')));
		$payload->expects($this->once())->method('loadPlugins')->with(array('foo'))->will($this->returnValue(array()));
		$payload->process();
	}

	public function testValidate() {
		$data = file_get_contents('tests/fixtures/sample-payload.json');
		$secret = 'dummysecret';
		$hash = hash_hmac('sha1', $data, $secret);
		$payload = $this->getMock(
			'NamelessCoder\\Gizzle\\Payload',
			array('readSignatureHeader', 'isCommandLine'),
			array($data, $secret)
		);
		$payload->expects($this->once())->method('isCommandLine')->will($this->returnValue(FALSE));
		$payload->expects($this->once())->method('readSignatureHeader')->will($this->returnValue('sha1=' . $hash));
		$payload->__construct($data, $secret);
	}

	public function testValidateThrowsRuntimeExceptionOnHashMismatch() {
		$data = file_get_contents('tests/fixtures/sample-payload.json');
		$secret = 'dummysecret';
		$hash = hash_hmac('sha1', $data . 'appendforinvalidchecksum', $secret);
		$payload = $this->getMock(
			'NamelessCoder\\Gizzle\\Payload',
			array('readSignatureHeader', 'isCommandLine'),
			array($data, $secret),
			'',
			FALSE
		);
		$payload->expects($this->once())->method('isCommandLine')->will($this->returnValue(FALSE));
		$payload->expects($this->once())->method('readSignatureHeader')->will($this->returnValue('sha1=' . $hash));
		$this->setExpectedException('RuntimeException', '', 1411225210);
		$payload->__construct($data, $secret);
	}

	public function testReadSignatureHeaderReadsFromServerVariable() {
		$data = file_get_contents('tests/fixtures/sample-payload.json');
		$secret = 'dummysecret';
		$hash = hash_hmac('sha1', $data, $secret);
		$_SERVER['HTTP_X_HUB_SIGNATURE'] = 'sha1=' . $hash;
		$payload = new Payload($data, $secret);
		unset($_SERVER['HTTP_X_HUB_SIGNATURE']);
	}

	public function testResponseContainsErrorCodeAndErrorsWhenPluginsCauseErrors() {
		$errorPlugin = new ErrorPlugin();
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('loadPluginsFromPackage', 'validate'), array('{}', ''));
		$payload->expects($this->once())->method('loadPluginsFromPackage')
			->with('NamelessCoder\\Gizzle')
			->will($this->returnValue(array($errorPlugin)));
		$payload->loadPlugins('NamelessCoder\\Gizzle');
		$result = $payload->process();
		$this->assertInstanceOf('NamelessCoder\\Gizzle\\Response', $result);
		$this->assertEquals(1, $result->getCode());
		$errors = $result->getErrors();
		$error = $errors[0];
		$this->assertInstanceOf('RuntimeException', $error);
		$this->assertEquals(1411238763, $error->getCode());
	}

	public function testHasResponseAfterProcessing() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('validate'), array('{}', ''));
		$response = $payload->process();
		$result = $payload->getResponse();
		$this->assertEquals($response, $result);
	}

	/**
	 * @dataProvider getPropertyValueDataSets
	 * @param string $property
	 * @param mixed $value
	 */
	public function testGetterAndSetter($property, $value) {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('loadPluginsFromPackage', 'validate'), array('{}', ''));
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
			array('action', 'action'),
			array('branches', array(new Branch(), new Branch())),
			array('parent', uniqid()),
			array('child', uniqid()),
			array('commits', array(new Commit(), new Commit())),
			array('comparisonUrl', uniqid()),
			array('context', uniqid()),
			array('created', TRUE),
			array('deleted', TRUE),
			array('forced', TRUE),
			array('head', new Commit()),
			array('sender', new Entity()),
			array('organization', new Entity()),
			array('ref', uniqid()),
			array('refName', uniqid()),
			array('repository', new Repository()),
			array('api', new Api()),
			array('pullRequest', new PullRequest())
		);
	}

	public function testSendMessageStoresMessage() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('loadPluginsFromPackage', 'validate'), array('{}', ''));
		$message = new Message('Test');
		$payload->sendMessage($message);
		$this->assertContains($message, $this->getObjectAttribute($payload, 'messages'));
	}

	public function testSendMessageIgnoresDuplicates() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('loadPluginsFromPackage', 'validate'), array('{}', ''));
		$message = new Message('Test');
		$payload->sendMessage($message);
		$payload->sendMessage($message);
		$messages = $this->getObjectAttribute($payload, 'messages');
		$this->assertContains($message, $messages);
		$this->assertCount(1, $messages);
	}

	public function testSendMessageSetsPullRequestToPayloadPullRequestIfPullRequestAndCommitNotSpecified() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('loadPluginsFromPackage', 'validate'), array('{}', ''));
		$pullRequest = new PullRequest();
		$payload->setPullRequest($pullRequest);
		$message = new Message('Test');
		$payload->sendMessage($message);
		$messages = $this->getObjectAttribute($payload, 'messages');
		$id = spl_object_hash($message);
		$this->assertEquals($pullRequest, $messages[$id]->getPullRequest());
	}

	public function testSendMessageSetsCommitToPayloadHeadIfPayloadNotForPullRequestAndPullRequestAndCommitNotSpecified() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('loadPluginsFromPackage', 'validate'), array('{}', ''));
		$head = new Commit();
		$payload->setHead($head);
		$message = new Message('Test');
		$payload->sendMessage($message);
		$messages = $this->getObjectAttribute($payload, 'messages');
		$id = spl_object_hash($message);
		$this->assertEquals($head, $messages[$id]->getCommit());
		$this->assertNull($messages[$id]->getPullRequest());
	}

	public function testSendMessageSetsCommitAndPullRequestWithPriorityForPullRequest() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('loadPluginsFromPackage', 'validate'), array('{}', ''));
		$head = new Commit();
		$pullRequest = new PullRequest();
		$payload->setHead($head);
		$payload->setPullRequest($pullRequest);
		$message = new Message('Test');
		$id = spl_object_hash($message);
		$payload->sendMessage($message);
		$messages = $this->getObjectAttribute($payload, 'messages');
		$this->assertEquals($pullRequest, $messages[$id]->getPullRequest());
		$this->assertNull($messages[$id]->getCommit());
	}

	/**
	 * @param Message $message
	 * @param string $expected
	 * @dataProvider getGenerateSummaryOfMessageTestValues
	 */
	public function testGenerateSummaryOfMessage(Message $message, $expected) {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('loadPluginsFromPackage', 'validate'), array('{}', ''));
		$payload->sendMessage($message);
		$method = new \ReflectionMethod('NamelessCoder\\Gizzle\\Payload', 'generateSummaryOfMessage');
		$method->setAccessible(TRUE);
		$result = $method->invokeArgs($payload, array($message));
		$this->assertEquals($expected, $result);
	}

	public function testGenerateSummaryOfMessages() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('loadPluginsFromPackage', 'validate'), array('{}', ''));
		$messageDataSets = $this->getGenerateSummaryOfMessageTestValues();
		$expected = '';
		$messags = array();
		foreach ($messageDataSets as $messageData) {
			$payload->sendMessage($messageData[0]);
			$expected .= $messageData[1] . PHP_EOL . PHP_EOL;
			$messages[] = $messageData[0];
		}
		$method = new \ReflectionMethod('NamelessCoder\\Gizzle\\Payload', 'generateSummaryOfMessages');
		$method->setAccessible(TRUE);
		$result = $method->invokeArgs($payload, array($messages));
		$this->assertEquals($expected, $result);
	}

	/**
	 * @param Message $message
	 * @param string $expected
	 * @dataProvider getDispatchMessageTestValues
	 */
	public function testDispatchMessage(Message $message, $expected) {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('loadPluginsFromPackage', 'getApi'), array('{}', ''));
		$api = $this->getMock('Milo\\GitHub\\Api', array('post'));
		$payload->expects($this->once())->method('getApi')->willReturn($api);
		if (TRUE === $message->getCommit() instanceof Commit || TRUE === $message->getPullRequest() instanceof PullRequest) {
			$api->expects($this->once())->method('post')->with($this->anything(), json_encode($expected));
		} else {
			$api->expects($this->never())->method('post');
		}
		$method = new \ReflectionMethod('NamelessCoder\\Gizzle\\Payload', 'dispatchMessage');
		$method->setAccessible(TRUE);
		$method->invokeArgs($payload, array($message));
	}

	/**
	 * @return array
	 */
	public function getDispatchMessageTestValues() {
		$commit = new Commit();
		$commit->setId(321);
		$commit->setUrl('url');
		$pullRequest = new PullRequest();
		$pullRequest->setId(456);
		$pullRequest->setUrlReviewComments('urlreviewcomments');
		$pullRequest->setUrlComments('urlcomments');
		$withCommit = new Message('Message');
		$withCommit->setCommit($commit);
		$withCommitAndPath = new Message('Message', '/path/to/file', 123);
		$withCommitAndPath->setCommit($commit);
		$withPullRequest = new Message('Message');
		$withPullRequest->setPullRequest($pullRequest);
		$withPullRequestAndCommit = new Message('Message');
		$withPullRequestAndCommit->setPullRequest($pullRequest);
		$withPullRequestAndCommit->setCommit($commit);
		return array(
			array(new Message('Message'), array('body' => 'Message')),
			array(new Message('Message', '/path/to/file', 123), array('body' => 'Message')),
			array($withCommit, array('body' => 'Message', 'sha1' => 321)),
			array($withCommitAndPath, array('body' => 'Message', 'commit_id' => 321, 'path' => '/path/to/file', 'position' => 123)),
			array($withPullRequest, array('body' => 'Message', 'sha1' => 456)),
			array($withPullRequestAndCommit, array('body' => 'Message', 'sha1' => 321))
		);
	}

	/**
	 * @return array
	 */
	public function getGenerateSummaryOfMessageTestValues() {
		$pullRequest = new PullRequest();
		$pullRequest->setId('123');
		$withPullRequest = new Message('Test message with pull request');
		$withPullRequest->setPullRequest($pullRequest);
		$commit = new Commit();
		$commit->setId('456');
		$withCommit = new Message('Test message with commit');
		$withCommit->setCommit($commit);
		$withCommitAndPath = clone $withCommit;
		$withCommitAndPath->setPath('/path/to/file');
		$withCommitAndPath->setPosition(789);
		return array(
			array($withPullRequest, 'Pull Request: 123' . PHP_EOL . 'Test message with pull request'),
			array($withCommit, 'Commit: 456' . PHP_EOL . 'Test message with commit'),
			array($withCommitAndPath, 'Commit: 456' . PHP_EOL . 'File: /path/to/file' . PHP_EOL . 'Line: 789' . PHP_EOL . 'Test message with commit'),
		);
	}

	/**
	 * @param integer $numberOfMessages
	 * @param integer $limit
	 * @param Message $customMessage
	 * @param integer $expectedNumberOfMessages
	 * @dataProvider getDispatchMessagesTestValues
	 */
	public function testDispatchMessages($numberOfMessages, $limit, $customMessage, $expectedNumberOfMessages) {
		$payload = $this->getMock(
			'NamelessCoder\\Gizzle\\Payload',
			array('loadSettings', 'dispatchMessage'),
			array('{}', '')
		);
		$payload->expects($this->exactly($expectedNumberOfMessages))->method('dispatchMessage');
		$payload->expects($this->any())->method('loadSettings')->willReturn(array(Payload::OPTION_MAX_MESSAGES => Payload::OPTION_MAX_MESSAGES_DEFAULT));
		while (0 < $numberOfMessages) {
			$payload->sendMessage(new Message('Message #' . $numberOfMessages));
			--$numberOfMessages;
		}
		$payload->dispatchMessages($limit, $customMessage);
	}

	/**
	 * @return array
	 */
	public function getDispatchMessagesTestValues() {
		return array(
			array(1, NULL, NULL, 1),
			array(2, NULL, NULL, 2),
			array(3, 3, NULL, 3),
			array(4, 3, NULL, 1),
			array(10, 3, NULL, 1),
			array(10, 3, new Message('Custom overflow'), 1),
		);
	}

}
