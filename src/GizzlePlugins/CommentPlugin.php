<?php

/**
 * This file belongs to the NamelessCoder/Gizzle package
 *
 * Copyright (c) 2015, Claus Due
 *
 * Released under the MIT license, of which the full text
 * was distributed with this package in file LICENSE.txt
 */

namespace NamelessCoder\Gizzle\GizzlePlugins;

use NamelessCoder\Gizzle\AbstractPlugin;
use NamelessCoder\Gizzle\Payload;
use NamelessCoder\Gizzle\PluginInterface;

/**
 * Class CommentPlugin
 *
 * Plugin designed to be used as sub-plugin for events,
 * for example causing a new comment to be created for
 * a pull request or a commit. Can also be used as
 * last-item plugin to report the cumulative payload of
 * the entire request.
 */
class CommentPlugin extends AbstractPlugin implements PluginInterface {

	const OPTION_ENABLED = 'enabled';
	const OPTION_COMMENT = 'comment';
	const OPTION_PULL_REQUEST = 'pullRequest';
	const OPTION_COMMIT = 'commit';

	/**
	 * Trigger if enabled flag evaluates to TRUE and
	 * this Gizzle installation is configured with a
	 * personal access token (see README.md).
	 *
	 * Note: the token requirement is circumvented
	 * when this plugin is used as event handler.
	 *
	 * @param Payload $payload
	 * @return boolean
	 */
	public function trigger(Payload $payload) {
		$enabled = (boolean) (TRUE === isset($this->settings[self::OPTION_ENABLED]) ? $this->settings[self::OPTION_ENABLED] : TRUE);
		$api = $payload->getApi();
		return $enabled && FALSE === empty($api);
	}

	/**
	 * Place a comment using the configured comment plus
	 * a dump of payload data from the Payload. Set the
	 * comment for a pull request or commit as determined
	 * by the OPTION_PULL_REQUEST and OPTION_COMMIT flags
	 * (both of which are booleans).
	 *
	 * Both options can be set to TRUE, but if pull request
	 * commenting is enabled, it takes priority. To comment
	 * on the HEAD commit of a pull request, run this plugin
	 * with only the OPTION_COMMIT flag.
	 *
	 * @param Payload $payload
	 * @return void
	 */
	public function process(Payload $payload) {
		$commentSetting = $this->getSetting(self::OPTION_COMMENT);
		if (FALSE === empty($commentSetting)) {
			$commentSetting .= PHP_EOL;
		}
		$errors = $payload->getResponse()->getErrors();
		$output = $payload->getResponse()->getOutput();
		$commentString = $commentSetting
			. $this->renderErrorsAsMarkupList($errors)
			. $this->renderPayloadDataAsMarkupList($output);
		$commentString = trim($commentString);
		if (TRUE === (boolean) $this->getSettingValue(self::OPTION_PULL_REQUEST, FALSE)) {
			$payload->storePullRequestComment($payload->getPullRequest(), $commentString);
		} elseif (TRUE === (boolean) $this->getSettingValue(self::OPTION_COMMIT, FALSE)) {
			$payload->storeCommitComment($payload->getHead(), $commentString);
		}
	}

	/**
	 * Renders a deep array as a nested list of unordered
	 * menu items in Markdown format.
	 *
	 * @param array $data
	 * @param integer $indent
	 * @return string
	 */
	protected function renderPayloadDataAsMarkupList(array $data, $indent = 0) {
		$markup = '';
		foreach ($data as $key => $value) {
			$markup .= str_repeat(' ', $indent * 2) . '- ' . $key . ':';
			if (TRUE === is_array($value)) {
				$markup .= $this->renderPayloadDataAsMarkupList($value, ++ $indent);
			} else {
				$markup .= ' ' . $value . PHP_EOL;
			}
		}
		return TRUE === empty($markup) ? '' : PHP_EOL . $markup;
	}

	/**
	 * Render error messages (from Exceptions) as Markdown list.
	 *
	 * @param \Exception[] $errors
	 * @return string
	 */
	protected function renderErrorsAsMarkupList(array $errors) {
		$markup = '';
		foreach ($errors as $error) {
			$markup .= '- Error code: ' . $error->getCode() . ' - ' . $error->getMessage() . PHP_EOL;
		}
		return TRUE === empty($markup) ? '' : PHP_EOL . $markup;
	}

}
