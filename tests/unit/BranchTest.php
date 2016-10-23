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

use NamelessCoder\Gizzle\Branch;
use NamelessCoder\Gizzle\Commit;

/**
 * Class BranchTest
 */
class BranchTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var array
	 */
	protected $fixture = array(
		'name' => 'foobar',
		'commit' => array(
			'id' => '123',
			'sha1' => '123'
		)
	);

	public function testConstructorAcceptsFixtureJson() {
		new Branch($this->fixture);
	}

	/**
	 * @dataProvider getPropertyValueDataSets
	 * @param string $property
	 * @param mixed $value
	 */
	public function testGetterAndSetter($property, $value) {
		$payload = $this->getMockBuilder('NamelessCoder\\Gizzle\\Branch')->setMethods(array('__construct'))->setConstructorArgs(array($this->fixture, ''))->getMock();
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
			array('name', uniqid()),
			array('commit', new Commit()),
		);
	}

}
