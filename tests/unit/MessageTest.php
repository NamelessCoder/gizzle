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

use NamelessCoder\Gizzle\Commit;
use NamelessCoder\Gizzle\Message;
use NamelessCoder\Gizzle\PullRequest;

/**
 * Class MessageTest
 */
class MessageTest extends \PHPUnit_Framework_TestCase {

	public function testConstructorSetsValues() {
		$message = new Message('Test', '/path/to/file', 321);
		$this->assertEquals('Test', $message->getBody());
		$this->assertEquals('/path/to/file', $message->getPath());
		$this->assertEquals(321, $message->getPosition());
	}

	/**
	 * @param string $property
	 * @param mixed $value
	 * @dataProvider getPropertyGetterAndSetterTestValues
	 */
	public function testPropertyGetterAndSetter($property, $value) {
		$message = new Message();
		$setter = 'set' . ucfirst($property);
		$getter = 'get' . ucfirst($property);
		$message->$setter($value);
		$this->assertEquals($value, $message->$getter());
	}

	/**
	 * @return array
	 */
	public function getPropertyGetterAndSetterTestValues() {
		return array(
			array('body', 'I am a body'),
			array('path', '/path/to/file.txt'),
			array('position', 321),
			array('commit', new Commit()),
			array('pullRequest', new PullRequest())
		);
	}

	/**
	 * @param Message $message
	 * @param array $expected
	 * @dataProvider getToGitHubApiDataArrayTestValues
	 */
	public function testToGitHubApiDataArray(Message $message, array $expected) {
		$result = $message->toGitHubApiDataArray();
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return array
	 */
	public function getToGitHubApiDataArrayTestValues() {
		$commit = new Commit();
		$commit->setId(321);
		$pullRequest = new PullRequest();
		$pullRequest->setId(456);
		$withCommit = new Message('Message');
		$withCommit->setCommit($commit);
		$withCommitAndPath = new Message('Message', '/path/to/file', 123);
		$withCommitAndPath->setCommit($commit);
		$withPullRequest = new Message('Message');
		$withPullRequest->setPullRequest($pullRequest);
		return array(
			array(new Message('Message'), array('body' => 'Message')),
			array(new Message('Message', '/path/to/file', 123), array('body' => 'Message')),
			array($withCommit, array('body' => 'Message', 'sha1' => 321)),
			array($withCommitAndPath, array('body' => 'Message', 'path' => '/path/to/file', 'position' => 123, 'commit_id' => 321)),
			array($withPullRequest, array('body' => 'Message', 'sha1' => 456))
		);
	}

}
