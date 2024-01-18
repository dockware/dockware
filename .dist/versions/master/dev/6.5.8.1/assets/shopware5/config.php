<?php

return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'username' => 'root',
        'password' => 'root',
        'dbname' => 'shopware',
    ],
    'template' => [
        'forceCompile' => true,
    ],
    'phpsettings' => [
        'error_reporting' => E_ALL & ~E_USER_DEPRECATED,
        'display_errors' => 1,
    ],
    'front' => [
        'throwExceptions' => true,
        'showException' => true,
    ],
    'httpcache' => [
        'debug' => true,
    ],
];
