<?php

namespace Application;

use Application\Activity\ActivityManager;
use Application\Activity\ActivityManagerFactory;
use Application\Activity\Invoker;
use Application\Controller\ConsoleController;
use Application\Controller\IndexController;
use Application\Daemon\ContactClosureDaemon;
use Application\Daemon\MainDaemon;
use Application\Daemon\TestDaemon;
use Application\Service\Activity;
use Application\Service\ContactClosure as ContactClosureService;
use Application\Service\Daemon as DaemonService;
use Application\Service\Queue as QueueService;

return array_merge(
    include 'console.config.php',
    include 'doctrine.config.php',
    include 'routes.config.php',
    include 'assetic.config.php',
    [
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'factories' => [
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            ActivityManager::class => ActivityManagerFactory::class
        ],
        'invokables' => [
            /** Daemons */
            TestDaemon::class => TestDaemon::class,
            ContactClosureDaemon::class => ContactClosureDaemon::class,
            MainDaemon::class => MainDaemon::class,

            /** Services */
            Activity::class                 => Activity::class,
            ContactClosureService::class    => ContactClosureService::class,
            DaemonService::class            => DaemonService::class,
            QueueService::class             => QueueService::class,

            Invoker::class                  => Invoker::class,
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
            'Application\Controller\Console' => ConsoleController::class
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
