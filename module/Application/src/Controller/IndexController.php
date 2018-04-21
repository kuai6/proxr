<?php

namespace Application\Controller;

use Application\Hydrator\Rest\DeviceHydrator;
use Application\Service\DeviceService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

/**
 * Class IndexController
 * @package Application\Controller
 */
class IndexController extends AbstractActionController
{
    /**
     * @var DeviceService
     */
    private $deviceService;

    /**
     * IndexController constructor.
     * @param DeviceService $deviceService
     */
    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }


    public function indexAction()
    {
        return new JsonModel([]);
    }

    public function devicesAction()
    {
        $devices = $this->deviceService->devices();

        $hydrator= new DeviceHydrator();
        $result = [];

        foreach ($devices as $device) {
            $result[] = $hydrator->extract($device);
        }
        return new JsonModel($result);
    }
}
