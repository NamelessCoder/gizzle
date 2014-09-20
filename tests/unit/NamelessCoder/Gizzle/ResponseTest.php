<?php
namespace NamelessCoder\Gizzle\Tests\Unit;

/**
 * Class ResponseTest
 */
class ResponseTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider getPropertyValueDataSets
	 * @param string $property
	 * @param mixed $value
	 */
	public function testGetterAndSetter($property, $value) {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Response', array('__construct'), array('{}', ''));
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
			array('code', 1),
			array('errors', array(new \RuntimeException(), new \RuntimeException())),
		);
	}

}