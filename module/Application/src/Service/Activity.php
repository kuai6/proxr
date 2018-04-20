<?php

namespace Application\Service;

use Application\Activity\ActivityManager;
use Application\Activity\Invoker;
use Application\Event\Event;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class Activity
 * @package Application\Service
 */
class Activity extends AbstractService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ActivityManager
     */
    private $activityManager;

    /**
     * @var Invoker
     */
    private $activityInvoker;

    /**
     * Activity constructor.
     * @param EntityManager $entityManager
     * @param ActivityManager $activityManager
     * @param Invoker $activityInvoker
     */
    public function __construct(EntityManager $entityManager, ActivityManager $activityManager, Invoker $activityInvoker)
    {
        $this->entityManager = $entityManager;
        $this->activityManager = $activityManager;
        $this->activityInvoker = $activityInvoker;
    }


    /**
     * @param Event $event
     * @return bool
     */
    public function contactClosureEventHandler($event)
    {
        /** @var \Application\EntityRepository\Activity $activityRepository */
        $activityRepository = $this->entityManager->getRepository(\Application\Entity\Activity::class);
        $activities = $activityRepository->getActivitiesDBAL($event->getName(), $event->getDevice(), $event->getBank());

        foreach($activities as $activityData) {
            /** @var \Application\Activity\Activity\Activity $activity */
            $activity = $this->activityManager->get('activity');
            /** @var string $metadata */
            $this->activityInvoker->invoke($activity, [
                $activityData
            ], $activityData['metadata']);
        }

        return true;
    }
}