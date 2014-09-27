<?php
namespace NamelessCoder\Gizzle\GizzlePlugins;

use NamelessCoder\Gizzle\Payload;
use NamelessCoder\Gizzle\PluginInterface;

/**
 * Class SelfUpdatePlugin
 */
class SelfUpdatePlugin implements PluginInterface {

	const OPTION_ENABLED = 'enabled';
	const OPTION_BRANCH = 'branch';

	/**
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Initialize the plugin with an array of settings.
	 *
	 * @param array $settings
	 * @return void
	 */
	public function initialize(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * This plugin will always trigger
	 *
	 * @param Payload $payload
	 * @return boolean
	 */
	public function trigger(Payload $payload) {
		$enabled = (boolean) (TRUE === isset($this->settings[self::OPTION_ENABLED]) ? $this->settings[self::OPTION_ENABLED] : TRUE);
		$matchesBranch = TRUE === isset($this->settings[self::OPTION_BRANCH]) ? 'refs/heads/' . $this->settings[self::OPTION_BRANCH] === $payload->getRef() : TRUE;
		return $enabled && $matchesBranch;
	}

	/**
	 * Switch to repository root, pull and do composer update.
	 *
	 * @param Payload $payload
	 * @return void
	 */
	public function process(Payload $payload) {
		$output = array();
		$command = $this->getCommand();
		// run composer update
		$this->invokeShellCommand($command, $output);
	}

	/**
	 * @param string $command
	 * @param array $output
	 * @return integer
	 */
	protected function invokeShellCommand($command, &$output) {
		$code = 0;
		exec($command, $output, $code);
		return $code;
	}

	/**
	 * @return string
	 */
	protected function getCommand() {
		return 'cd .. && `which git` pull && `which composer` update --no-dev';
	}

}
