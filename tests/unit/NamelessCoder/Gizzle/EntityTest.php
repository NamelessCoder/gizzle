<?php
namespace NamelessCoder\Gizzle\Tests\Unit;
use NamelessCoder\Gizzle\Entity;

/**
 * Class EntityTest
 */
class EntityTest extends \PHPUnit_Framework_TestCase {

	public function testConstructorAcceptsFixtureJson() {
		$data = file_get_contents('tests/fixtures/sample-payload.json');
		$data = json_decode($data, JSON_OBJECT_AS_ARRAY);
		new Entity($data['repository']['owner']);
	}

	/**
	 * @dataProvider getPropertyValueDataSets
	 * @param string $property
	 * @param mixed $value
	 */
	public function testGetterAndSetter($property, $value) {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Entity', array('__construct'), array('{}', ''));
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
			array('email', uniqid()),
			array('username', uniqid()),
			array('url', uniqid()),
		);
	}

}