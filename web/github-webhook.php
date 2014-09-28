<?php

require_once '../vendor/autoload.php';

$secretFile = '../.secret';
$tokenFile = '../.token';
$secret = $token = NULL;
$output = array(
	'messages' => array()
);
if (TRUE === file_exists($secretFile)) {
	$secret = trim(file_get_contents($secretFile));
}
if (TRUE === file_exists($tokenFile)) {
	$token = trim(file_get_contents($tokenFile));
}
if ('cli' === php_sapi_name()) {
	$data = file_get_contents('php://stdin');
} else {
	$data = file_get_contents('php://input');
}

try {
	$settingsFileArgument = $_GET['settings'];
	$api = new \Milo\Github\Api();
	$api->setToken($token);
	if (TRUE === empty($settingsFileArgument)) {
		$payload = processSettingsFile('Settings.yml', $data, $secret, $token, $output, $api, $index);
	} elseif (FALSE === is_array($settingsFileArgument)) {
		$payload = processSettingsFile($settingsFileArgument, $data, $secret, $token, $output, $api, $index);
	} else {
		foreach ($settingsFileArgument as $index => $settingsFile) {
			processSettingsFile($settingsFile, $data, $secret, $token, $output, $api, $index);
		}
	}
	header('Content-type: application/json');
} catch (\RuntimeException $error) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', TRUE, 500);
	$output['messages'][] = $error->getMessage() . ' (' . $error->getCode() . ')';
	if (TRUE === isset($payload)) {
		setStatus($payload, $api, 'error', $buildNumber);
	} else {
		setStatus($payload, $api, 'failure', $buildNumber);
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
	$response = $payload->process();
	$payload = new \NamelessCoder\Gizzle\Payload($data, $secret, $settingsFile);
	$payload->setApi($api);
	setStatus($payload, $api, 'pending', $buildNumber);
	if (0 === $response->getCode()) {
		$output += $response->getOutput();
		setStatus($payload, $api, 'success', $buildNumber);
	} else {
		setStatus($payload, $api, 'error', $buildNumber);
		$output['messages'][] = 'The following errors were reported:';
		foreach ($response->getErrors() as $error) {
			$output['messages'][] = $error->getMessage() . ' (' . $error->getCode() . ')' . PHP_EOL;
		}
	}
	return $payload;
}

function setStatus(\NamelessCoder\Gizzle\Payload $payload, \Milo\Github\Api $api, $state, $buildNumber) {
	if (TRUE === empty($api->getToken())) {
		return;
	}
	$url = sprintf(
		'/repos/%s/%s/statuses/%s',
		$payload->getRepository()->getOwner()->getUsername(),
		$payload->getRepository()->getFullName(),
		$payload->getHead()->getSha1()
	);
	switch ($state) {
		case 'pending': $desription = 'Waiting to hear from Gizzle...'; break;
		case 'success': $desription = 'Gizzle build was successful!'; break;
		case 'error': $desription = 'Gizzle reported an error!'; break;
		case 'failure': $desription = 'Gizzle was unable to run the build!'; break;
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
