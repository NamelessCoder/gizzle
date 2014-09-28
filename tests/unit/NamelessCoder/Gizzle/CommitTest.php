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
use NamelessCoder\Gizzle\Entity;

/**
 * Class CommitTest
 */
class CommitTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var array
	 */
	protected $fixture = array(
		'id' => '123',
		'sha1' => '123'
	);

	public function testConstructorAcceptsFixtureJson() {
		new Commit($this->fixture);
	}

	/**
	 * @dataProvider getPropertyValueDataSets
	 * @param string $property
	 * @param mixed $value
	 */
	public function testGetterAndSetter($property, $value) {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Commit', array('__construct'), array($this->fixture, ''));
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
			array('added', array(uniqid(), uniqid())),
			array('author', new Entity()),
			array('committer', new Entity()),
			array('distinct', TRUE),
			array('id', uniqid()),
			array('sha1', uniqid()),
			array('message', uniqid()),
			array('parents', array(new Commit(), new Commit())),
			array('modified', array(uniqid(), uniqid())),
			array('removed', array(uniqid(), uniqid())),
			array('timestamp', \DateTime::createFromFormat('U', time())),
			array('url', uniqid()),
		);
	}

}
