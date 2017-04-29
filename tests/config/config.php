<?php

return [
    'components' => [
        'db' => [
            'dsn' => 'mysql:host=localhost;dbname=test_test',
        ],
        'urlManager' => [
            'enablePrettyUrl' => false,
            'showScriptName' => true,
        ],
    ],
];