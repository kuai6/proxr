<?php

return [
    'assetic_configuration' => [
        // Use on production environment
        'debug' => false,
        'buildOnRequest' => false,

        // this is specific to this project
        'webPath' => __DIR__ . '/../../public/assets',
        'basePath' => 'assets',
    ],
];
