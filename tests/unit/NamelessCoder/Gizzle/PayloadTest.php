<?php
namespace NamelessCoder\Gizzle\Tests\Unit;

use NamelessCoder\Gizzle\Payload;
use NamelessCoder\Gizzle\Tests\Fixtures\GizzlePlugins\Plugin;

/**
 * Class PayloadTest
 */
class PayloadTest extends \PHPUnit_Framework_TestCase {

	public function testLoadPlugins() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('validate', 'loadPluginsFromPackage'), array('{}', ''));
		$payload->expects($this->once())->method('loadPluginsFromPackage')
			->with('NamelessCoder\\Gizzle')
			->will($this->returnValue(array()));
		$payload->loadPlugins('NamelessCoder\\Gizzle');
	}

	public function testLoadPluginsLoadsExpectedPlugins() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('validate'), array('{}', ''));
		$payload->loadPlugins('NamelessCoder\\Gizzle\\Tests\\Fixtures');
		$this->assertAttributeEquals(array(new Plugin()), 'plugins', $payload);
	}

	public function testLoadsAndMapsPayloadData() {
		$data = file_get_contents('tests/fixtures/sample-payload.json');
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('validate'), array($data, ''));
		$payload->loadPlugins('NamelessCoder\\Gizzle\\Tests\\Fixtures');
		$result = $payload->process();
		$this->assertInstanceOf('NamelessCoder\\Gizzle\\Response', $result);
		$this->assertEquals(0, $result->getCode());
	}

}
