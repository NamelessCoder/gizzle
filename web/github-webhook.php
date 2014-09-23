<?php

require_once '../vendor/autoload.php';

$secretFile = '../.secret';
$secret = NULL;
if (TRUE === file_exists($secretFile)) {
	$secret = trim(file_get_contents($secretFile));
}
$data = file_get_contents('php://input');

try {
	$payload = new \NamelessCoder\Gizzle\Payload($data, $secret);
	$respone = $payload->process();
	if (0 < $respone->getCode()) {
		echo 'The following errors were reported:' . PHP_EOL;
		foreach ($respone->getErrors() as $error) {
			echo $error->getMessage() . ' (' . $error->getCode() . ')' . PHP_EOL;
		}
	}
} catch (\RuntimeException $error) {
	echo $error->getMessage() . ' (' . $error->getCode() . ')';
}
