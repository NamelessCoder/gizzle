<?php

require_once '../vendor/autoload.php';

$secretFile = '../.secret';
$secret = NULL;
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
		processSettingsFile('Settings.yml', $data, $secret);
	} elseif (FALSE === is_array($settingsFileArgument)) {
		processSettingsFile($settingsFileArgument, $data, $secret);
	} else {
		foreach ($settingsFileArgument as $settingsFile) {
			processSettingsFile($settingsFile, $data, $secret);
		}
	}
} catch (\RuntimeException $error) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', TRUE, 500);
	echo $error->getMessage() . ' (' . $error->getCode() . ')';
}


function processSettingsFile($settingsFile, $data, $secret) {
	$allowedPattern = '/[^a-z0-9\/]+\.yml/i';
	$settingsFile = urldecode($settingsFile);
	$settingsFile = trim($settingsFile, './\\'); // no absolutes or dot-files, including escaped ones.
	$settingsFile = preg_match($allowedPattern, $settingsFile) ? : $settingsFile; // nullify if invalid
	$payload = new \NamelessCoder\Gizzle\Payload($data, $secret, $settingsFile);
	$response = $payload->process();
	if (0 === $response->getCode()) {
		$output = $response->getOutput();
		header('Content-type: application/json');
		echo json_encode($output, JSON_HEX_QUOT | JSON_PRETTY_PRINT);
	} else {
		header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', TRUE, 500);
		echo 'The following errors were reported:' . PHP_EOL;
		foreach ($response->getErrors() as $error) {
			echo $error->getMessage() . ' (' . $error->getCode() . ')' . PHP_EOL;
		}
	}
}
