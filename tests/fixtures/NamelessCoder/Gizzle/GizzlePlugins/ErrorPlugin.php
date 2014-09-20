<?php
namespace NamelessCoder\Gizzle\Tests\Fixtures\GizzlePlugins;

use NamelessCoder\Gizzle\Payload;
use NamelessCoder\Gizzle\PluginInterface;

/**
 * Class ErrorPlugin
 */
class ErrorPlugin implements PluginInterface {

	/**
	 * Analyse $payload and return TRUE if this plugin should
	 * be triggered in processing the payload.
	 *
	 * @param Payload $payload
	 * @return boolean
	 */
	public function trigger(Payload $payload) {
		return TRUE;
	}

	/**
	 * Perform whichever task the Plugin should perform based
	 * on the payload's data.
	 *
	 * @param Payload $payload
	 * @return void
	 * @throws \RuntimeException
	 */
	public function process(Payload $payload) {
		throw new \RuntimeException('Test Exception', 1411238763);
	}

}