<?php

namespace Application\Service;

use Application\Activity\ActivityManager;
use Application\Activity\Invoker;
use Application\Event\Event;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class Activity
 * @package Application\Service
 */
class ActivityListener extends AbstractListenerAggregate
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
        if (sizeof($activities) === 0) {
            return false;
        }

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

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(Event::EVENT_CONTACT_CLOSURE,  [$this, 'contactClosureEventHandler']);
    }
}