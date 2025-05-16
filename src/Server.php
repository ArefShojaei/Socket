<?php

namespace SocketIO;

use Exception;
use Socket;
use SocketIO\Mixins\{
    HasDispatcher,
    HasEncryption,
    HasHttp
};


final class Server {
    use HasDispatcher, HasEncryption, HasHttp;

    private array $clients = [];

    private array $headers = [];

    private string $host;

    private int $port;

    private Socket $socket;


    public function __construct(string $host, int $port) {
        $this->host = $host;

        $this->port = $port;

        $this->start();
    }

    private function start(): void {
        $this->socket = SocketFactory::create($this->host, $this->port);
    }

    public function listen(): void {
        Log::info("WebSocket server is listening on ws://{$this->host}:{$this->port}");

        try {
            while (true) {
                $readSockets = array_merge([$this->socket], $this->clients);
                socket_select($readSockets, $null, $null, 0, 10);
    
                // Check for new connections
                if (in_array($this->socket, $readSockets)) {
                    $clientSocket = socket_accept($this->socket);
                    $headers = socket_read($clientSocket, 1024);
    
                    $this->emit("request", $headers);
    
                    // Perform WebSocket handshake
                    $this->performHandshake($clientSocket, explode("\n", $headers));
    
                    // Add the new client to the list of clients
                    $this->clients[] = $clientSocket;
    
                    $this->emit("connection", $clientSocket);
                    // echo "New client connected.\n";
    
                    // Remove the master socket from the read list
                    unset($readSockets[array_search($this->socket, $readSockets)]);
                }
    
                // Check for messages from clients
                foreach ($readSockets as $readSocket) {
                    $data = socket_read($readSocket, 1024);
    
                    if (!$data) {
                        // Client has disconnected
                        $this->emit("close", $clientSocket);
    
                        unset($this->clients[array_search($readSocket, $this->clients)]);
                        // echo "Client disconnected.\n";
    
                        continue;
                    }
    
                    // Decode and broadcast the message to all clients
                    // $payload = $this->unmask($data);
                    $payload = $this->unmask($data);
    
                    if ($payload) {
                        $this->emit("message", $payload);
                        // echo "Received message: $payload\n";
                        foreach ($this->clients as $client) {
                            $this->send($client, $payload);
                        }
                    }
                }
            }
    
            socket_close($this->socket);
        } catch (Exception $error) {
            $this->emit("error", $error->getMessage());
        }
    }

    public function send(Socket $client, string $message): void {
        $encodedMessage = $this->mask($message);

        socket_write($client, $encodedMessage, strlen($encodedMessage));
    }
}
