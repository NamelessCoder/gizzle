<?php
namespace NamelessCoder\Gizzle\Tests\Unit;
use NamelessCoder\Gizzle\Entity;

/**
 * Class RepositoryTest
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider getPropertyValueDataSets
	 * @param string $property
	 * @param mixed $value
	 */
	public function testGetterAndSetter($property, $value) {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Repository', array('__construct'), array('{}', ''));
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
			array('created', \DateTime::createFromFormat('U', time() - rand(0, 9999))),
			array('description', uniqid()),
			array('forks', rand(99,999)),
			array('fork', TRUE),
			array('hasDownloads', TRUE),
			array('hasIssues', TRUE),
			array('hasWiki', TRUE),
			array('homepage', uniqid()),
			array('id', rand(99,999)),
			array('language', uniqid()),
			array('name', uniqid()),
			array('masterBranch', uniqid()),
			array('private', TRUE),
			array('openIssues', rand(99,999)),
			array('pushed', \DateTime::createFromFormat('U', time() - rand(0, 9999))),
			array('size', rand(99,999)),
			array('stargazers', rand(99,999)),
			array('url', uniqid()),
			array('watchers', rand(99,999)),
			array('owner', new Entity()),
		);
	}

}
