<?php

namespace Server;

use Server\Controller\Console\ServerController;
use Server\Controller\Console\ServerControllerFactory;

return [
    'factories' => [
        ServerController::class => ServerControllerFactory::class,
    ],
];