<?php

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'device' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/device',
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'view' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/view/:id',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => 'Application\Controller\Device',
                                'action' => 'view'
                            ],
                        ],
                    ],
                ],
            ],
            'event' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/event',
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'last' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/last/:deviceId/:ts',
                            'constraints' => [
                                'deviceId' => '[0-9]*',
                                'ts' => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => 'Application\Controller\Event',
                                'action' => 'last'
                            ],
                        ],
                    ],
                    'device' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/device/:deviceId',
                            'constraints' => [
                                'deviceId' => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => 'Application\Controller\Event',
                                'action' => 'device'
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
