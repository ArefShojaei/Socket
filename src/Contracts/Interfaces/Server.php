<?php

namespace SocketIO\Contracts\Interfaces;

use Socket;


interface Server {
    public function send(Socket $client, string $message): void;
    public function listen();
}