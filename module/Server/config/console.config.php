<?php

namespace Server;

use Server\Controller\Console\ServerController;

return [
    'console' => [
        'router' => [
            'routes' => [
                'server-run' => [
                    'options' => [
                        'route' => 'server run',
                        'defaults' => [
                            'controller' => ServerController::class,
                            'action' => 'run',
                        ],
                    ],
                ],
            ],
        ],
    ],
];