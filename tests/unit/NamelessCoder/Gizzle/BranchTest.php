<?php
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
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Branch', array('__construct'), array($this->fixture, ''));
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
