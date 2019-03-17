<?php

namespace Application\Controller;

use Application\Entity\Periphery\PeripheryType;
use Application\Hydrator\Rest\PeripheryExtractor;
use Application\Hydrator\Rest\PeripheryTypeMapper;
use Application\Service\PeripheryService;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class PeripheryController extends AbstractActionController
{
    /**
     * @var PeripheryService
     */
    private $peripheryService;

    /**
     * @var PeripheryTypeMapper
     */
    private $peripheryTypeMapper;

    /**
     * @var PeripheryExtractor
     */
    private $peripheryUnitMapper;

    /**
     * PeripheryController constructor.
     * @param PeripheryService $peripheryService
     * @param PeripheryTypeMapper $peripheryTypeMapper
     * @param PeripheryExtractor $perypheryUnitMapper
     */
    public function __construct(PeripheryService $peripheryService, PeripheryTypeMapper $peripheryTypeMapper, PeripheryExtractor $perypheryUnitMapper)
    {
        $this->peripheryService = $peripheryService;
        $this->peripheryTypeMapper = $peripheryTypeMapper;
        $this->peripheryUnitMapper = $perypheryUnitMapper;
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
    public function listPeripheryTypesAction()
    {
        $types = $this->peripheryService->listTypes();
        $result = [];

        foreach ($types as $peripheryType)
        {
            $result[] =$this->peripheryTypeMapper->extract($peripheryType);
        }
        return new JsonModel($result);
    }

    public function listAllPeripheryAction()
    {
        $units = $this->peripheryService->listAllUnits();
        $result = [];

        foreach ($units as $peripheryUnit)
        {
            $result[] = $this->peripheryUnitMapper->extract($peripheryUnit);
        }
        return new JsonModel([$result]);
    }

    public function listDevicePeripheryAction()
    {
        $device_id = $this->getEvent()->getRouteMatch()->getParam('$device_id');
        $units = $this->peripheryService->listDeviceUnits($device_id);
        $result = [];

        foreach ($units as $peripheryUnit)
        {
            $result[] = $this->peripheryUnitMapper->extract($peripheryUnit);
        }
        return new JsonModel([$result]);
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
    public function registerPeripheryTypeAction()
    {
        $request = Json::decode($this->request->getContent(), Json::TYPE_ARRAY);
        $requestObject = $this->peripheryTypeMapper->hydrate($request, new PeripheryType());

        $result = $this->peripheryService->createType($requestObject);
        return new JsonModel($this->peripheryTypeMapper->extract($result));
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
    public function connectDevicePeripheryAction()
    {
        $device_id = $this->getEvent()->getRouteMatch()->getParam("device_id");
        $periphery_type = $this->getEvent()->getRouteMatch()->getParam("periphery_type");

        $periphery = $this->peripheryService->registerUnit($device_id, $periphery_type);
        return new JsonModel($this->peripheryUnitMapper->extract($periphery));
    }
}
