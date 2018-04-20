<?php

namespace Application;

use Application\Activity\ActivityManager;
use Application\Activity\ActivityManagerFactory;
use Application\Activity\Invoker;
use Application\Activity\InvokerFactory;
use Application\Controller\ConsoleController;
use Application\Controller\ConsoleControllerFactory;
use Application\Controller\IndexController;
use Application\Daemon\ContactClosureDaemon;
use Application\Daemon\MainDaemon;
use Application\Daemon\TestDaemon;
use Application\Daemon\UdpDaemon;
use Application\Listener\IncomeListener;
use Application\Listener\IncomeListenerFactory;
use Application\Service\Activity;
use Application\Service\ActivityFactory;
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
use Kuai6\Queue\ServerFactory;

return array_merge(
    include 'console.config.php',
    include 'doctrine.config.php',
    [
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
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/application',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                            ],
                        ],
                    ],
                ],
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
            Activity::class         => ActivityFactory::class,
            QueueService::class     => QueueFactory::class,
            Invoker::class          => InvokerFactory::class,

            DeviceService::class    => DeviceServiceFactory::class,
            BankService::class      => BankServiceFactory::class,
            IncomeListener::class   => IncomeListenerFactory::class,
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
            'ApplicationEntityManager' => 'doctrine.entity_manager.orm_default'
        ]
    ],
    'translator' => [
        'locale' => 'en_US',
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Application\Controller\Index' => IndexController::class,
        ],
        'factories' => [
            'Application\Controller\Console' => ConsoleControllerFactory::class,
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
    ],
]);
