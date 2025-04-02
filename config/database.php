<?php

return [
    'driver'   => 'mysql',
    'host'     => 'localhost',
    'database' => 'framejam',
    'username' => 'root',
    'password' => '',
    'port'     => '3307',
    'options'  => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
]; 