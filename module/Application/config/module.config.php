<?php

namespace Application;

use Application\Activity\ActivityManager;
use Application\Activity\ActivityManagerFactory;
use Application\Activity\Invoker;
use Application\Activity\InvokerFactory;
use Application\Controller\ConsoleController;
use Application\Controller\ConsoleControllerFactory;
use Application\Controller\DeviceController;
use Application\Controller\DeviceControllerFactory;
use Application\Controller\IndexController;
use Application\Controller\IndexControllerFactory;
use Application\Controller\PeripheryController;
use Application\Controller\PeripheryControllerFactory;
use Application\Daemon\ContactClosureDaemon;
use Application\Daemon\MainDaemon;
use Application\Daemon\TestDaemon;
use Application\Daemon\UdpDaemon;
use Application\Listener\IncomeListener;
use Application\Listener\IncomeListenerFactory;
use Application\Listener\RenderListener;
use Application\Options\ModuleOptions;
use Application\Options\ModuleOptionsFactory;
use Application\Service\ActivityListener;
use Application\Service\ActivityFactory;
use Application\Service\ActivityService;
use Application\Service\ActivityServiceFactory;
use Application\Service\BankService;
use Application\Service\BankServiceFactory;
use Application\Service\ContactClosure as ContactClosureService;
use Application\Service\Daemon as DaemonService;
use Application\Service\DeviceService;
use Application\Service\DeviceServiceFactory;
use Application\Service\Queue as QueueService;
use Application\Service\QueueFactory;
use Application\Service\UdpService;
use Application\Service\UdpServiceFactory;
use Application\Service\PeripheryService;
use Application\Service\PeripheryServiceFactory;
use Application\Listener\RenderListenerFactory;
use Zend\Mvc\Router\Http\Literal;
use Zend\Mvc\Router\Http\Method;
use Zend\Mvc\Router\Http\Segment;

return array_merge(
    include 'console.config.php',
    include 'doctrine.config.php',
    [
    'router' => [
        'routes' => [
            'home' => [
                'type' =>  Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'devices' => [
                'type' => Literal::class,
                'options' => [
                    'verb'     => 'GET',
                    'route'    => '/rest/v1/devices',
                    'defaults' => [
                        'controller' => DeviceController::class,
                        'action'     => 'listDevices',
                    ],
                    'may_terminate' => true
                ],
            ],
            'device-periphery' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/rest/v1/devices/:device_id/periphery/:periphery_type',
                    'may_terminate' => true,
                    'child_routes' => [
                        'list-periphery' => [
                            'type' => Method::class,
                            'options' => [
                                'verb' => 'GET',
                                'defaults' => [
                                    'controller' => PeripheryController::class,
                                    'action' => 'listDevicePeriphery'
                                ]
                            ]
                        ],
                        'connect-periphery' => [
                            'type' => Method::class,
                            'options' => [
                                'verb' => 'POST',
                                'defaults' => [
                                    'controller' => PeripheryController::class,
                                    'action'     => 'connectDevicePeriphery',
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'all-periphery' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/rest/v1/periphery',
                    'verb' => 'GET',
                    'defaults' => [
                        'controller' => PeripheryController::class,
                        'action'     => 'listAllPeriphery',
                    ]
                ],
                'may_terminate' => true,
            ],
            'periphery-types' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/rest/v1/periphery/types'
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'list' => [
                        'type' => Method::class,
                        'options' => [
                            'verb' => 'GET',
                            'defaults' => [
                                'controller' => PeripheryController::class,
                                'action'     => 'listPeripheryTypes',
                            ]
                        ]
                    ],
                    'create' => [
                        'type' => Method::class,
                        'options' => [
                            'verb' => 'POST',
                            'defaults' => [
                                'controller' => PeripheryController::class,
                                'action'     => 'registerPeripheryType',
                            ]
                        ]
                    ]
                ]
            ],
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'factories' => [
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            ActivityManager::class  => ActivityManagerFactory::class,

            /** Services */
            ActivityListener::class         => ActivityFactory::class,
            QueueService::class     => QueueFactory::class,
            Invoker::class          => InvokerFactory::class,

            DeviceService::class    => DeviceServiceFactory::class,
            BankService::class      => BankServiceFactory::class,
            PeripheryService::class => PeripheryServiceFactory::class,
            IncomeListener::class   => IncomeListenerFactory::class,
            RenderListener::class   => RenderListenerFactory::class,

            ActivityService::class => ActivityServiceFactory::class,
            ModuleOptions::class   => ModuleOptionsFactory::class,
        ],
        'invokables' => [
            /** Daemons */
            TestDaemon::class => TestDaemon::class,
            ContactClosureDaemon::class => ContactClosureDaemon::class,
            MainDaemon::class => MainDaemon::class,
            UdpDaemon::class => UdpDaemon::class,


            ContactClosureService::class    => ContactClosureService::class,
            DaemonService::class            => DaemonService::class,


        ],
        'aliases' =>[
            'ApplicationEntityManager' => 'doctrine.entitymanager.orm_default'
        ]
    ],
    'controllers' => [
        'factories' => [
            ConsoleController::class => ConsoleControllerFactory::class,
            IndexController::class   => IndexControllerFactory::class,
            DeviceController::class   => DeviceControllerFactory::class,
            PeripheryController::class => PeripheryControllerFactory::class,
            ActivityService::class => ActivityServiceFactory::class
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'application' => [
        'modulePath' => __DIR__ .'/../',
    ],
]);
