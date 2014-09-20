<?php

require_once '../vendor/autoload.php';

$secretFile = '../.secret';
$secret = NULL;
if (TRUE === file_exists($secretFile)) {
	$secret = file_get_contents($secretFile);
}
$data = file_get_contents('php://input');

$payload = new \NamelessCoder\Gizzle\Payload($data, $secret);
$payload->loadPlugins('NamelessCoder\\Gizzle');
$payload->run();
