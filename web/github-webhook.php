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
	$payload->loadPlugins('NamelessCoder\\Gizzle');
	$payload->process();
} catch (\RuntimeException $error) {
	echo $error->getMessage() . ' (' . $error->getCode() . ')';
}
