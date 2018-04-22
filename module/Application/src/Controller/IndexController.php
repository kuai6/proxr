<?php

namespace Application\Controller;

use Application\Hydrator\Rest\DeviceHydrator;
use Application\Service\ActivityService;
use Application\Service\DeviceService;
use Doctrine\Common\Util\Debug;
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
     * @var ActivityService
     */
    private $activityService;

    /**
     * IndexController constructor.
     * @param DeviceService $deviceService
     * @param ActivityService $activityService
     */
    public function __construct(DeviceService $deviceService, ActivityService $activityService)
    {
        $this->deviceService = $deviceService;
        $this->activityService = $activityService;
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

    /**
     * @return JsonModel
     */
    public function connectAction()
    {
        $request = $this->getRequest();
        $obj = json_decode($request->getContent());
        $this->activityService->create($obj->source->device->id, $obj->source->port, $obj->script);

        return new JsonModel(['result' => 'ok']);
    }
}
