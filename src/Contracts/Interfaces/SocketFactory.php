<?php

namespace SocketIO\Contracts\Interfaces;

use Socket;


interface SocketFactory {
    public static function create(string $host, int $port): Socket;
}