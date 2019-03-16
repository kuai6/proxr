<?php

namespace Application\Controller;

use Application\Options\ModuleOptions;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use OpenApi\Annotations as OA;

/**
 *
 * @OA\Info(title="Shelled Controller", version="0.1")
 * @OA\Server(url=API_HOST)
 *
 * Class IndexController
 * @package Application\Controller
 */
class IndexController extends AbstractActionController
{

    /**
     * @var ModuleOptions
     */
    private $moduleOptions;

    /**
     * IndexController constructor.
     * @param ModuleOptions $moduleOptions
     */
    public function __construct(ModuleOptions $moduleOptions)
    {
        $this->moduleOptions = $moduleOptions;
    }

    /**
     * @return mixed|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $openapi = \OpenApi\scan($this->moduleOptions->getModulePath());

        return new JsonModel(json_decode($openapi->toJson(), true));
    }

}
