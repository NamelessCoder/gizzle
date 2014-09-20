<?php
namespace NamelessCoder\Gizzle\Tests\Unit;

use NamelessCoder\Gizzle\Payload;

/**
 * Class PayloadTest
 */
class PayloadTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @provider
	 */
	public function testLoadPlugins() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('validate'), array('{}', ''));
		$payload->loadPlugins('NamelessCoder\\Gizzle');
	}

}
