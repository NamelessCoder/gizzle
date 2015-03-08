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

use NamelessCoder\Gizzle\Base;
use NamelessCoder\Gizzle\Commit;
use NamelessCoder\Gizzle\Entity;
use NamelessCoder\Gizzle\PullRequest;
use NamelessCoder\Gizzle\Repository;

/**
 * Class PullRequestTest
 */
class PullRequestTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider getPropertyValueDataSets
	 * @param string $property
	 * @param mixed $value
	 */
	public function testGetterAndSetter($property, $value) {
		$pullRequest = new PullRequest();
		$getter = 'get' . ucfirst($property);
		$setter = 'set' . ucfirst($property);
		$pullRequest->$setter($value);
		$this->assertEquals($value, $pullRequest->$getter());
	}

	/**
	 * @return array
	 */
	public function getPropertyValueDataSets() {
		return array(
			array('id', 'id'),
			array('head', new Commit()),
			array('base', new Base()),
			array('body', 'body'),
			array('changedFiles', rand()),
			array('comments', rand()),
			array('commits', rand()),
			array('additions', rand()),
			array('deletions', rand()),
			array('assignee', new Entity()),
			array('milestone', 'milestone'),
			array('number', rand()),
			array('dateCreated', new \DateTime('now')),
			array('dateUpdated', new \DateTime('now')),
			array('dateClosed', new \DateTime('now')),
			array('state', 'state'),
			array('locked', TRUE),
			array('merged', TRUE),
			array('mergeable', TRUE),
			array('mergeCommitSha', 'mergeCommitSha'),
			array('mergeableState', 'mergeableState'),
			array('mergedBy', new Entity()),
			array('reviewComments', rand()),
			array('title', 'title'),
			array('user', new Entity())
		);
	}

}
