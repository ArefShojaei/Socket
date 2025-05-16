<?php

namespace SocketIO;

use Socket;
use SocketIO\Contracts\Interfaces\SocketFactory as ISocketFactory;


final class SocketFactory implements ISocketFactory {
    public static function create(string $host, int $port): Socket {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

        socket_bind($socket, $host, $port);

        socket_listen($socket);

        return $socket;
    }
}