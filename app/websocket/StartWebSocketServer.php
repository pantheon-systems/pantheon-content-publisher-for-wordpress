<?php

require __DIR__ . '/../../vendor/autoload.php';

use Ratchet\App;
use PCC\websocket\WebSocketServer;

if ($argc < 4) {
	echo "Usage: php start-server.php <host> <port> <path>\n";
	exit(1);
}

$host = 'pantheon.xyz';
$port = 443;
$path = 'websocket';

$server = new App($host, $port);
$server->route($path, new WebSocketServer(), ['*']);
$server->run();
