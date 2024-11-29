<?php
require dirname(__DIR__) . '/chatserver/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class VideoChatServer implements MessageComponentInterface {
    protected $clients;
    protected $rooms;

    public function __construct() {
        $this->clients = new \SplObjectStorage;  // Storage for all connected clients
        $this->rooms = [];  // Maps room IDs to clients
    }

    // When a new connection is established
    public function onOpen(ConnectionInterface $conn) {
        echo "New connection: ({$conn->resourceId})\n";
        $this->clients->attach($conn);
    }

    // Handling incoming messages from clients
    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);  // Parse the incoming JSON message
        
        if ($data['type'] === 'chat') {
            // Send chat messages to all clients in the room
            foreach ($this->clients as $client) {
                if (in_array($data['room_id'], $this->rooms[$client->resourceId] ?? [])) {
                    // Only send message to clients in the same room
                    $client->send(json_encode($data));
                }
            }
        } elseif ($data['type'] === 'join') {
            // Add the client to the room
            $this->rooms[$from->resourceId][] = $data['room_id'];
            echo "Client {$from->resourceId} joined room {$data['room_id']}\n";
        } elseif ($data['type'] === 'leave') {
            // Remove the client from the room
            if (isset($this->rooms[$from->resourceId])) {
                $key = array_search($data['room_id'], $this->rooms[$from->resourceId]);
                if ($key !== false) {
                    unset($this->rooms[$from->resourceId][$key]);
                }
                echo "Client {$from->resourceId} left room {$data['room_id']}\n";
            }
        }
    }

    // When the connection is closed
    public function onClose(ConnectionInterface $conn) {
        echo "Connection closed: ({$conn->resourceId})\n";
        $this->clients->detach($conn);  // Remove the client from the list
        // Clean up any room assignments
        if (isset($this->rooms[$conn->resourceId])) {
            unset($this->rooms[$conn->resourceId]);
        }
    }

    // Handling errors
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

// Create the WebSocket server and wrap it in an HTTP server for handling WebSocket traffic
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new VideoChatServer()  // The video chat server logic
        )
    ),
    8080,
    '0.0.0.0'  // Bind to all interfaces (adjust as needed for your setup)
);

// Run the server
$server->run();
