<?php

return [
    'driver' => 'redis',
    'host' => '127.0.0.1',
    'port' => 6379,
    'password' => null,
    'database' => 0,
    'prefix' => 'framejam:queue:',
    'default' => 'default',
    'failed' => 'failed',
    'retry_after' => 90,
    'block_for' => null,
]; 