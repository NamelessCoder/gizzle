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
		$payload = new Payload('{}');
		$payload->loadPlugins('NamelessCoder\\Gizzle');
	}

}
