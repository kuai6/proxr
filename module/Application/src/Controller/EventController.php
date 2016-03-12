<?php

namespace Application\Controller;

use Application\Entity\EventLog;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

/**
 * Class EventsController
 * @package Application\Controller
 */
class EventController extends AbstractActionController
{
    public function indexAction()
    {

    }

    /**
     * @return array
     */
    public function deviceAction()
    {
        $deviceId = $this->params()->fromRoute('deviceId');

        return [
            'deviceId' => $deviceId
        ];

    }

    public function lastAction()
    {
        $timeStamp = $this->params()->fromRoute('ts');
        $deviceId = $this->params()->fromRoute('deviceId');

        /** @var EntityManager $entityManager */
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        /** @var \Application\EntityRepository\EventLog $eventLogRepository */
        $eventLogRepository = $entityManager->getRepository(EventLog::class);

        //$dateTime = new \DateTime()
        list($usec, $sec) = explode(" ", microtime());
        $dateTime = sprintf('%s.%06d', date('Y-m-d H:i:s', $sec), $usec*1000000);

        $dateTime =  new \DateTime($dateTime);

        $events = $eventLogRepository->getLast(1, $dateTime);

        return new JsonModel([
            'events' => $events,
            'sdfsdf' => 'dfgsdfgdf'
        ]);
    }
}