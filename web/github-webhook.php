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
	$settingsFileArgument = urldecode($_GET['settings']);
	if (TRUE === empty($settingsFileArgument)) {
		$payload = new \NamelessCoder\Gizzle\Payload($data, $secret);
	} else {
		$allowedPattern = '/[^a-z0-9\/]+\.yml/i';
		$settingsFileArgument = trim($settingsFileArgument, './\\'); // no absolutes or dot-files, including escaped ones.
		$settingsFileArgument = preg_match($allowedPattern, $settingsFileArgument) ? : $settingsFileArgument; // nullify if invalid
		$payload = new \NamelessCoder\Gizzle\Payload($data, $secret, $settingsFileArgument);
	}
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
} catch (\RuntimeException $error) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', TRUE, 500);
	echo $error->getMessage() . ' (' . $error->getCode() . ')';
}
