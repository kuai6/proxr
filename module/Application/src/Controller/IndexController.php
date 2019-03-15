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
     *     path="/rest/api/v1/devices",
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


    /**
     *
     * List all available periphery types
     *
     * @OA\Schema(
     *     schema="PeripheryType",
     *     @OA\Property(property="id", type="integer", description="type id", nullable=true),
     *     @OA\Property(property="name", type="string", description="name"),
     *     @OA\Property(property="description", type="string", description="description", nullable=true),
     *     @OA\Property(property="icon", type="string", description="icon path", nullable=true),
     *     @OA\Property(property="inputs", type="integer", description="inputs count"),
     *     @OA\Property(property="outputs", type="integer", description="outputs count")
     * )
     *
     * @OA\Get(
     *     path="/rest/api/v1/periphery",
     *     @OA\Response(response="200", description="Periphery types list",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/PeripheryType"))
     *     )
     * )
     *
     * @return JsonModel
     */
    public function listPeripheryTypes()
    {
    }

    /**
     * Register new periphery type
     *
     * @OA\Post(
     *     path="/rest/api/v1/periphery",
     *     @OA\RequestBody(description="Periphery type information", required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PeripheryType")
     *     ),
     *     @OA\Response(response="201", description="Registered successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PeripheryType")
     *     )
     * )
     *
     */
    public function registerPeripheryType()
    {
    }

    /**
     * Connect periphery to device
     *
     * @OA\Schema(
     *     schema="Periphery",
     *     @OA\Property(property="id", type="integer", description="periphery id"),
     *     @OA\Property(property="type_id", type="integer", description="type id"),
     *     @OA\Property(property="device_id", type="integer", description="device id"),
     *     @OA\Property(property="bank_id", type="integer", description="bank id"),
     *     @OA\Property(property="bit", type="integer", description="bit")
     * )
     *
     * @OA\Post(
     *     path="/rest/api/v1/devices/{device_id}/periphery/{periphery_type_id}",
     *     @OA\Parameter(name="device_id", in="path", required=true,
     *         @OA\Schema(type="integer"), description="device id"),
     *     @OA\Parameter(name="periphery_type_id", in="path", required=true,
     *         @OA\Schema(type="integer"), description="periphery type id"),
     *     @OA\Response(response="201", description="Connected successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Periphery")
     *     )
     * )
     */
    public function connectPeriphery()
    {}

    /**
     * Create activity
     *
     * @OA\Schema(
     *     schema="CreateActivityRequest",
     *     @OA\Property(property="source_id", type="integer", description="source peripheral id"),
     *     @OA\Property(property="dest_ids", type="array", @OA\Items(type="integer"), description="destination peripherals ids")
     * )
     *
     * @OA\Schema(
     *     schema="Activity",
     *     @OA\Property(property="id", type="integer", description="activity id", nullable=true),
     *     @OA\Property(property="device_id", type="integer", description="device id", nullable=true),
     *     @OA\Property(property="bank_id", type="integer", description="bank id", nullable=true),
     *     @OA\Property(property="bit", type="integer", description="bit", nullable=true),
     *     @OA\Property(property="metadata", type="string", nullable=true)
     * )
     *
     * @OA\Post(
     *     path="/rest/api/v1/activities",
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/CreateActivityRequest")),
     *     @OA\Response(response="201", description="Activity created successfully",
     *             @OA\JsonContent(ref="#/components/schemas/Activity")
     *         )
     *     )
     */
    public function linkPeripheries()
    {}

    /**
     * Update activity
     *
     * @OA\Put(
     *     path="/rest/api/v1/activities/{id}",
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/Activity")),
     *     @OA\Parameter(name="id", in="path", required=true,
     *         @OA\Schema(type="integer"), description="activity id"),
     *     @OA\Response(response="200", description="Updated successfully")
     * )
     */
    public function updateActivity()
    {}
}
