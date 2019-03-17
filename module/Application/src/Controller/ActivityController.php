<?php

namespace Application\Controller;

use Application\Entity\Activity;
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

    public function createActivityAction()
    {
        $request = Json::decode($this->request->getContent(), Json::TYPE_ARRAY);
        /** @var Activity $requestObject */
        $requestObject = $this->activityMapper->hydrate($request, new Activity());

        $result = $this->activityService->create($requestObject->getDevice()->getId(), $requestObject->getBit());
        return new JsonModel($this->activityMapper->extract($result));
    }

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
        $activity_id = $this->getEvent()->getRouteMatch()->getParam("activity_id");

        $activity = $this->activityService->get($activity_id);
        return new JsonModel($this->activityMapper->extract($activity));
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
        $activity_id = $this->getEvent()->getRouteMatch()->getParam("activity_id");
        $request = Json::decode($this->request->getContent(), Json::TYPE_ARRAY);
        /** @var Activity $requestObject */
        $requestObject = $this->activityMapper->hydrate($request, new Activity());

        $activity = $this->activityService->update($activity_id, $requestObject);
        return new JsonModel($this->activityMapper->extract($activity));
    }
}
