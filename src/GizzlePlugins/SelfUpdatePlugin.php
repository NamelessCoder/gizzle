<?php

/**
 * This file belongs to the NamelessCoder/Gizzle package
 *
 * Copyright (c) 2014, Claus Due
 *
 * Released under the MIT license, of which the full text
 * was distributed with this package in file LICENSE.txt
 */

namespace NamelessCoder\Gizzle\GizzlePlugins;

use NamelessCoder\Gizzle\AbstractPlugin;
use NamelessCoder\Gizzle\Payload;
use NamelessCoder\Gizzle\PluginInterface;

/**
 * Class SelfUpdatePlugin
 */
class SelfUpdatePlugin extends AbstractPlugin implements PluginInterface {

	const OPTION_ENABLED = 'enabled';
	const OPTION_BRANCH = 'branch';
	const OPTION_GITCOMMAND = 'gitCommand';
	const OPTION_COMPOSERCOMMAND = 'composerCommand';

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
		$payload->getResponse()->addOutputFromPlugin($this, $output);
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
		$gitCommand = $this->getSettingValue(self::OPTION_GITCOMMAND, '`which git`');
		$composerCommand = $this->getSettingValue(self::OPTION_COMPOSERCOMMAND, '`which composer`');
		return 'cd ..; ' . $gitCommand . ' pull; COMPOSER_HOME="/tmp/" ' . $composerCommand . ' install --no-dev --no-ansi';
	}

}
