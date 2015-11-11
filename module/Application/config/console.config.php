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
                'daemon' => [
                    'options' => [
                        'route' => 'daemon <daemonName> (start|stop|restart):command',
                        'defaults' => [
                            'controller' => 'Application\Controller\Console',
                            'action' => 'daemon',
                        ],
                    ],
                ],
                'contact closure daemon' => [
                    'options' => [
                        'route' => 'contactClosureDeviceDaemon',
                        'defaults' => [
                            'controller' => 'Application\Controller\Console',
                            'action' => 'contactClosureDeviceDaemon',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
