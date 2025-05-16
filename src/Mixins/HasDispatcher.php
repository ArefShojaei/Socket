<?php

namespace SocketIO\Mixins;


trait HasDispatcher {
    public function on(string $event, callable $callback): void {
        $this->events[$event] = $callback;
    }

    private function emit(string $event, mixed ...$params): void {
        $callback = $this->events[$event];

        call_user_func($callback, ...$params);
    }
}