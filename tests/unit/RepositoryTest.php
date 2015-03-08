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

use NamelessCoder\Gizzle\Entity;
use NamelessCoder\Gizzle\Repository;

/**
 * Class RepositoryTest
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase {

	public function testConstructorAcceptsFixtureJson() {
		$data = file_get_contents('tests/fixtures/sample-payload.json');
		$data = json_decode($data, JSON_OBJECT_AS_ARRAY);
		new Repository($data['repository']);
	}

	/**
	 * @dataProvider getPropertyValueDataSets
	 * @param string $property
	 * @param mixed $value
	 */
	public function testGetterAndSetter($property, $value) {
		$repository = $this->getMock('NamelessCoder\\Gizzle\\Repository', array('dummy'));
		$getter = 'get' . ucfirst($property);
		$setter = 'set' . ucfirst($property);
		$repository->$setter($value);
		$this->assertEquals($value, $repository->$getter());
	}

	/**
	 * @return array
	 */
	public function getPropertyValueDataSets() {
		return array(
			array('created', \DateTime::createFromFormat('U', time() - rand(0, 9999))),
			array('description', uniqid()),
			array('forks', rand(99, 999)),
			array('fork', TRUE),
			array('fullName', uniqid()),
			array('hasDownloads', TRUE),
			array('hasIssues', TRUE),
			array('hasWiki', TRUE),
			array('hasPages', TRUE),
			array('homepage', uniqid()),
			array('id', rand(99, 999)),
			array('language', uniqid()),
			array('name', uniqid()),
			array('masterBranch', uniqid()),
			array('private', TRUE),
			array('openIssues', rand(99, 999)),
			array('pushed', \DateTime::createFromFormat('U', time() - rand(0, 9999))),
			array('size', rand(99, 999)),
			array('stargazers', rand(99, 999)),
			array('watchers', rand(99, 999)),
			array('owner', new Entity()),
		);
	}

	/**
	 * @return void
	 */
	public function testSetAndGetApiUrls() {
		$repository = new Repository();
		$urls = array(
			Repository::API_URL_COMMENTS => 'test1',
			Repository::API_URL_ASSIGNEES => 'test2'
		);
		$repository->setApiUrls($urls);
		foreach ($urls as $url => $value) {
			$result = $repository->resolveApiUrl($url);
			$this->assertEquals($value, $result);
		}
	}

}
