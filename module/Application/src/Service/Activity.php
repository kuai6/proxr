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
class Activity extends AbstractService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @param Event $event
     * @return bool
     */
    public function contactClosureEventHandler($event)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getServiceLocator()->get('ApplicationEntityManager');

        /** @var \Application\EntityRepository\Activity $activityRepository */
        $activityRepository = $entityManager->getRepository(\Application\Entity\Activity::class);

        $activities = $activityRepository->getActivitiesDBAL($event->getName(), $event->getDevice(), $event->getBank());
        /** @var Invoker $activityInvoker */
        $activityInvoker = $this->getServiceLocator()->get(Invoker::class);
        /** @var ActivityManager $activityManager */
        $activityManager = $this->getServiceLocator()->get(ActivityManager::class);

        foreach($activities as $activityData) {
            /** @var \Application\Activity\Activity\Activity $activity */
            $activity = $activityManager->get('activity');
            /** @var string $metadata */
            $activityInvoker->invoke($activity, [
                $activityData
            ], $activityData['metadata']);
        }

        return true;
    }
}