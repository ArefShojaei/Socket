<?php

namespace SocketIO;

use SocketIO\Contracts\Interfaces\Log as ILog;


final class Log implements ILog {
    public const INFO = "INFO";

    public static function info(string $message): void {
        echo "[" . self::INFO . "]". "{$message}" . PHP_EOL;
    }
}