<?php

require_once '../vendor/autoload.php';

$secretFile = '../.secret';
$secret = NULL;
$output = array(
	'messages' => array()
);
if (TRUE === file_exists($secretFile)) {
	$secret = trim(file_get_contents($secretFile));
}
if ('cli' === php_sapi_name()) {
	$data = file_get_contents('php://stdin');
} else {
	$data = file_get_contents('php://input');
}

try {
	$settingsFileArgument = $_GET['settings'];
	if (TRUE === empty($settingsFileArgument)) {
		processSettingsFile('Settings.yml', $data, $secret, $output);
	} elseif (FALSE === is_array($settingsFileArgument)) {
		processSettingsFile($settingsFileArgument, $data, $secret, $output);
	} else {
		foreach ($settingsFileArgument as $settingsFile) {
			processSettingsFile($settingsFile, $data, $secret, $output);
		}
	}
	header('Content-type: application/json');
} catch (\RuntimeException $error) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', TRUE, 500);
	$output['messages'][] = $error->getMessage() . ' (' . $error->getCode() . ')';
}

if (0 === count($output['messages'])) {
	unset($output['messages']);
}

echo json_encode($output, JSON_HEX_QUOT | JSON_PRETTY_PRINT);


function processSettingsFile($settingsFile, $data, $secret, &$output) {
	$allowedPattern = '/[^a-z0-9\/]+\.yml/i';
	$settingsFile = urldecode($settingsFile);
	$settingsFile = trim($settingsFile, './\\'); // no absolutes or dot-files, including escaped ones.
	$settingsFile = preg_match($allowedPattern, $settingsFile) ? : $settingsFile; // nullify if invalid
	$payload = new \NamelessCoder\Gizzle\Payload($data, $secret, $settingsFile);
	$response = $payload->process();
	if (0 === $response->getCode()) {
		$output += $response->getOutput();
	} else {
		$output['messages'][] = 'The following errors were reported:';
		foreach ($response->getErrors() as $error) {
			$output['messages'][] = $error->getMessage() . ' (' . $error->getCode() . ')' . PHP_EOL;
		}
	}
}
