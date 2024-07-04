<?php

namespace PCC\websocket;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class WebSocketServer implements MessageComponentInterface
{
	protected $clients;

	public function __construct()
	{
		$this->clients = new \SplObjectStorage();
	}

	public function onOpen(ConnectionInterface $conn)
	{
		$this->clients->attach($conn);
	}

	public function onMessage(ConnectionInterface $from, $msg)
	{
		// Broadcast message to all connected clients (including sender)
		foreach ($this->clients as $client) {
			$client->send($msg);
		}
	}

	public function onClose(ConnectionInterface $conn)
	{
		$this->clients->detach($conn);
	}

	public function onError(ConnectionInterface $conn, \Exception $e)
	{
		$conn->close();
	}
}
