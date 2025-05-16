<?php

namespace SocketIO\Mixins;


trait HasEncryption {
    private function decodeMessage($payload, $mask): string {
        # Assuming decodeMessage is supposed to unmask the message
        $decoded = "";

        for ($i = 0; $i < strlen($payload); $i++) {
            # Unmask data using the mask
            $decoded .= $payload[$i] ^ $mask[$i % 4];
        }
        return $decoded;
    }

    private function unmask($data): ?string {
        if (!$data) return null; # Handle case when no data is received
    
        $length = ord($data[1]) & 127;
    
        if ($length === 126) {
            $length = unpack("n", substr($data, 2, 2))[1];
            $mask = substr($data, 4, 4);
            return $this->decodeMessage(substr($data, 8), $mask);
        } elseif ($length === 127) {
            # Handle large messages (optional)
            return ""; # Handle this case as needed
        } else {
            $mask = substr($data, 2, 4);
            return $this->decodeMessage(substr($data, 6), $mask);
        }
    }

    private function mask($message): string {
        $length = strlen($message);

        if ($length <= 125) {
            return chr(129) . chr($length) . $message;
        } elseif ($length > 125 && $length < 65536) {
            return chr(129) . chr(126) . pack('n', $length) . $message;
        } else {
            # For large messages (not implemented here)
            return "";
        }
    }
}
