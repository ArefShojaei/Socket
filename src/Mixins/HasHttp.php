<?php

namespace SocketIO\Mixins;


trait HasHttp {
    private function getSocketKey(array $headers): ?string {
        $key = null;

        foreach ($headers as $header) {
            if (preg_match('/Sec-WebSocket-Key: (.*)$/', $header, $matches)) {
                $key = trim($matches[1]);
                break;
            }
        }

        return $key;
    }

    private function generateSocketHeaders(string $acceptKey): string {
        $headers = [];

        $headers[] = "HTTP/1.1 101 Switching Protocols";
        $headers[] = "Upgrade: websocket";
        $headers[] = "Connection: Upgrade";
        $headers[] = "Sec-WebSocket-Accept: {$acceptKey}";

        return implode("\r\n", $headers);
    }

    private function performHandshake($clientSocket, $headers): void {
        $key = $this->getSocketKey($headers);

        $acceptKey = base64_encode(pack('H*', sha1($key)));

        $response = $this->generateSocketHeaders($acceptKey);

        socket_write($clientSocket, $response, strlen($response));

        $this->emit("headers", $response);
    }
}