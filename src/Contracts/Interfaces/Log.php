<?php

namespace SocketIO\Contracts\Interfaces;


interface Log {
    public static function info(string $message): void;
}