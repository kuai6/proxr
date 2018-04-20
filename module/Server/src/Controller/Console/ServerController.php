<?php

namespace Server\Controller\Console;

use Server\Options\ModuleOptions;
use Server\Service\ServerService;
use Zend\Mvc\Controller\AbstractConsoleController;

/**
 * Class ServerController
 * @package Server\Controller\Console
 */
class ServerController extends AbstractConsoleController
{
    /**
     * @var ModuleOptions
     */
    private $options;

    /**
     * @var ServerService
     */
    private $serverService;

    /**
     * ServerController constructor.
     * @param ModuleOptions $options
     * @param ServerService $serverService
     */
    public function __construct(ModuleOptions $options, ServerService $serverService)
    {
        $this->options = $options;
        $this->serverService = $serverService;
    }


    public function runAction()
    {
        $this->serverService->run();
    }
}