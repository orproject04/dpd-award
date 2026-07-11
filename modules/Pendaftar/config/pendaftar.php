<?php

return [
    'routes' => [
        'enabled' => true,
        'middleware' => ['web', 'auth'],
        'prefix' => '',
    ],
    'view' => [
        'layout' => 'laravolt::layouts.app',
    ],
    'menu' => [
        'enabled' => false,
    ],
    'permission' => [],
];
