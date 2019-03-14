<?php

namespace Application\Controller;

use Application\Hydrator\Rest\DeviceHydrator;
use Application\Options\ModuleOptions;
use Application\Service\ActivityService;
use Application\Service\DeviceService;
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
     * @var DeviceService
     */
    private $deviceService;

    /**
     * @var ActivityService
     */
    private $activityService;

    /**
     * IndexController constructor.
     * @param ModuleOptions $moduleOptions
     * @param DeviceService $deviceService
     * @param ActivityService $activityService
     */
    public function __construct(
        ModuleOptions $moduleOptions,
        DeviceService $deviceService,
        ActivityService $activityService)
    {
        $this->moduleOptions = $moduleOptions;
        $this->deviceService = $deviceService;
        $this->activityService = $activityService;
    }

    /**
     * @return mixed|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $openapi = \OpenApi\scan($this->moduleOptions->getModulePath());

        return new JsonModel(json_decode($openapi->toJson(), true));
    }

    /**
     * Fetch devices list
     *
     * @OA\Schema(
     *   schema="Device",
     *   @OA\Property(property="id", type="integer", description="device id"),
     *   @OA\Property(property="numberOfPins", type="integer", description="number of pins"),
     *   @OA\Property(property="serialNumber", type="string", description="serial number"),
     *   @OA\Property(property="label", type="string", description="label"),
     *   @OA\Property(property="type", type="string", description="device type")
     * )
     *
     *
     * @OA\Get(
     *     path="/rest/api/v1/",
     *     @OA\Response(response="200", description="Fetch Devices list",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Device")
     *          )
     *     )
     * )
     *
     * @return JsonModel
     */
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
