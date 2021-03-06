<?php

/**
 * This file belongs to the NamelessCoder/Gizzle package
 *
 * Copyright (c) 2014, Claus Due
 *
 * Released under the MIT license, of which the full text
 * was distributed with this package in file LICENSE.txt
 */

define('GIZZLE_HOME', rtrim(realpath('../'), '/') . '/');

require_once GIZZLE_HOME . 'vendor/autoload.php';

$secretFile = GIZZLE_HOME . '.secret';
$tokenFile = GIZZLE_HOME . '.token';
$secret = $token = NULL;
$output = array(
	'messages' => array()
);
if (TRUE === file_exists($secretFile)) {
	$secret = trim(file_get_contents($secretFile));
}
if (TRUE === file_exists($tokenFile)) {
	$tokenSecret = trim(file_get_contents($tokenFile));
	$token = new \Milo\Github\OAuth\Token($tokenSecret);
}
if ('cli' === php_sapi_name()) {
	$data = file_get_contents('php://stdin');
} else {
	$data = file_get_contents('php://input');
}

try {
	$settingsFileArgument = $_GET['settings'];
	$api = new \Milo\Github\Api();
	if (NULL !== $token) {
		$api->setToken($token);
	}
	if (TRUE === empty($settingsFileArgument)) {
		$payload = processSettingsFile('Settings.yml', $data, $secret, $output, $api);
	} elseif (FALSE === is_array($settingsFileArgument)) {
		$payload = processSettingsFile($settingsFileArgument, $data, $secret, $output, $api);
	} else {
		foreach ($settingsFileArgument as $index => $settingsFile) {
			processSettingsFile($settingsFile, $data, $secret, $output, $api, $index);
		}
	}
	header('Content-type: application/json');
} catch (\RuntimeException $error) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', TRUE, 500);
	$output['messages'][] = $error->getMessage() . ' (' . $error->getCode() . ')';
	if (TRUE === isset($payload)) {
		setStatus($payload, $api, 'error', $buildNumber);
	}
}

if (0 === count($output['messages'])) {
	unset($output['messages']);
}

echo json_encode($output, JSON_HEX_QUOT | JSON_PRETTY_PRINT);


function processSettingsFile($settingsFile, $data, $secret, &$output, \Milo\Github\Api $api, $index = 0) {
	$buildNumber = $index + 1;
	$allowedPattern = '/[^a-z0-9\/]+\.yml/i';
	$settingsFile = urldecode($settingsFile);
	$settingsFile = trim($settingsFile, './\\'); // no absolutes or dot-files, including escaped ones.
	$settingsFile = preg_match($allowedPattern, $settingsFile) ? : $settingsFile; // nullify if invalid
	$payload = new \NamelessCoder\Gizzle\Payload($data, $secret, $settingsFile);
	$payload->setApi($api);
	setStatus($payload, $api, 'pending', $buildNumber);
	$response = $payload->process();
	$payload->dispatchMessages();
	if (0 === $response->getCode()) {
		$output += $response->getOutput();
		setStatus($payload, $api, 'success', $buildNumber);
	} else {
		$output['messages'][] = 'The following errors were reported:';
		foreach ($response->getErrors() as $error) {
			$output['messages'][] = $error->getMessage() . ' (' . $error->getCode() . ')' . PHP_EOL;
		}
		setStatus($payload, $api, 'error', $buildNumber);
	}
	return $payload;
}

function setStatus(\NamelessCoder\Gizzle\Payload $payload, \Milo\Github\Api $api, $state, $buildNumber) {
	$token = $api->getToken();
	$head = $payload->getHead();
	if (TRUE === empty($token) || TRUE === empty($head)) {
		return;
	}
	$url = sprintf(
		'/repos/%s/%s/statuses/%s',
		$payload->getRepository()->getOwner()->getName(),
		$payload->getRepository()->getName(),
		$head->getId()
	);
	switch ($state) {
		case 'pending': $description = 'Waiting to hear from Gizzle...'; break;
		case 'success': $description = 'Gizzle build was successful!'; break;
		case 'error': $description = 'Gizzle reported an error!'; break;
		case 'failure': $description = 'Gizzle was unable to run the build!'; break;
		default:
	}
	$data = array(
		'state' => $state,
		'target_url' => 'http://github.com/NamelessCoder/gizzle',
		'context' => $payload->getRepository()->getFullName() . '-gizzle-build-' . $buildNumber,
		'description' => $description
	);
	$api->post($url, $data);
}
