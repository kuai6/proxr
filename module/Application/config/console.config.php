<?php

return [
    'console' => [
        'router' => [
            'routes' => [
                'test daemon' => [
                    'options' => [
                        'route' => 'test (start|stop|restart):command [--logPath=] [--processPath=] [--childNumber=]',
                        'defaults' => [
                            'controller' => 'Application\Controller\Console',
                            'action' => 'test',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
