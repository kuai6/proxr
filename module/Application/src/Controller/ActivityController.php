<?php

namespace Application\Controller;

use Application\Hydrator\Rest\ActivityMapper;
use Application\Service\ActivityService;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class ActivityController extends AbstractActionController
{
    /** * @var ActivityService */
    private $activityService;

    /** @var ActivityMapper */
    private $activityMapper;

    /**
     * ActivityController constructor.
     * @param ActivityService $activityService
     * @param ActivityMapper $activityMapper
     */
    public function __construct(ActivityService $activityService, ActivityMapper $activityMapper)
    {
        $this->activityService = $activityService;
        $this->activityMapper = $activityMapper;
    }

    /**
     * @return JsonModel
     */
    public function connectAction()
    {
        $request = $this->getRequest();
        $obj = Json::decode($request->getContent());
        $this->activityService->create($obj->source->device->id, $obj->source->port, $obj->script);

        return new JsonModel(['result' => 'ok']);
    }

//    public function createActivityAction()
//    {
//        $mapper = new ActivityMapper($this->);
//
//        $request = Json::decode($this->request->getContent(), Json::TYPE_ARRAY);
//        $requestObject = $mapper->hydrate($request, new Activity());
//
//        $result = $this->activityService->create($requestObject);
//        return new JsonModel($mapper->extract($result));
//    }

    public function listActivitiesAction()
    {
        $activities = $this->activityService->list();

        $result = [];
        foreach ($activities as $activity) {
            $result[] = $this->activityMapper->extract($activity);
        }

        return new JsonModel($result);
    }

    public function getActivityAction()
    {

    }

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
    public function updateActivityAction()
    {

    }
}