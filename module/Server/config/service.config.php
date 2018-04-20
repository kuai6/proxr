<?php

namespace Server;

use Server\Listener\OutcomeListener;
use Server\Listener\OutcomeListenerFactory;
use Server\Options\ModuleOptions;
use Server\Options\ModuleOptionsFactory;
use Server\Service\ServerService;
use Server\Service\ServerServiceFactory;

return [
    'factories' => [
        ModuleOptions::class => ModuleOptionsFactory::class,

        ServerService::class => ServerServiceFactory::class,
        OutcomeListener::class => OutcomeListenerFactory::class,
    ],
];
