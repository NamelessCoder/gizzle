<?php
namespace NamelessCoder\Gizzle\Tests\Unit;

/**
 * Class AbstractPluginTest
 */
class AbstractPluginTest extends \PHPUnit_Framework_TestCase {

	public function testInitializeSetsSettings() {
		$mock = $this->getMockForAbstractClass('NamelessCoder\\Gizzle\\AbstractPlugin');
		$mock->initialize(array('foo' => 'bar'));
		$result = $this->getObjectAttribute($mock, 'settings');
		$this->assertEquals(array('foo' => 'bar'), $result);
	}

	public function testTriggerReturnsTrue() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array(), array(), '', FALSE);
		$mock = $this->getMockForAbstractClass('NamelessCoder\\Gizzle\\AbstractPlugin');
		$result = $mock->trigger($payload);
		$this->assertTrue($result);
	}

	public function testProcessDoesNothing() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('getResponse'), array(), '', FALSE);
		$payload->expects($this->never())->method('getResponse');
		$mock = $this->getMockForAbstractClass('NamelessCoder\\Gizzle\\AbstractPlugin');
		$result = $mock->process($payload);
	}

}
