<?php
namespace NamelessCoder\Gizzle\Tests\Unit;
use NamelessCoder\Gizzle\Commit;
use NamelessCoder\Gizzle\Entity;

/**
 * Class CommitTest
 */
class CommitTest extends \PHPUnit_Framework_TestCase {

	public function testConstructorAcceptsFixtureJson() {
		$data = file_get_contents('tests/fixtures/sample-payload.json');
		$data = json_decode($data, JSON_OBJECT_AS_ARRAY);
		new Commit($data['commit']);
	}

	/**
	 * @dataProvider getPropertyValueDataSets
	 * @param string $property
	 * @param mixed $value
	 */
	public function testGetterAndSetter($property, $value) {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Commit', array('__construct'), array('{}', ''));
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
