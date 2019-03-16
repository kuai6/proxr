<?php

namespace Application\Controller;

use Application\Hydrator\Rest\DeviceHydrator;
use Application\Service\DeviceService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class DeviceController extends AbstractActionController
{
    /**
     * @var DeviceService
     */
    private $deviceService;

    /**
     * @var DeviceHydrator
    */
    private $deviceMapper;

    /**
     * DeviceController constructor.
     * @param DeviceService $deviceService
     * @param DeviceHydrator $deviceMapper
     */
    public function __construct(DeviceService $deviceService, DeviceHydrator $deviceMapper)
    {
        $this->deviceService = $deviceService;
        $this->deviceMapper = $deviceMapper;
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
    public function listDevicesAction()
    {
        $devices = $this->deviceService->devices();
        $result = [];

        foreach ($devices as $device) {
            $result[] = $this->deviceMapper->extract($device);
        }
        return new JsonModel($result);
    }
}
