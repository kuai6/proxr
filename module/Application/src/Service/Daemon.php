<?php

namespace Application\Service;

use Application\Daemon\ContactClosureDaemon;
use Application\Entity\Bank;
use Application\Entity\EventLog;
use Application\Event\Event;
use Doctrine\ORM\EntityManager;
use Kuai6\Queue\Message;
use Kuai6\Queue\Queue;
use Kuai6\Queue\Server;
use Kuai6\Queue\ServerFactory;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Application;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class Daemon
 * @package Application\Service
 */
class Daemon extends AbstractService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     *
     */
    public function mainDaemonCycle()
    {
        /** @var Server $queueServer */
        $queueServer = $this->getServiceLocator()->get(ServerFactory::class);
        $queue = new Queue('app.daemon.main.queue');
        $queueServer->consume($queue, [$this, 'handleMainDaemonMessage']);
    }

    /**
     * @param Message $message
     * @return bool
     * @throws \Exception
     */
    public function handleMainDaemonMessage($message)
    {
        try {
            /** @var EntityManager $entityManager */
            $entityManager = $this->getServiceLocator()->get('ApplicationEntityManager');
            /** @var Application $application */
            $application = $this->getServiceLocator()->get('Application');
            /** @var EventManagerInterface $eventManager */
            $eventManager = $application->getEventManager();

            $data = $message->getData();
            if (!array_key_exists('deviceId', $data) || !array_key_exists('deviceBanks', $data)) {
                return false;
            }
            foreach ($data['deviceBanks'] as $bankName => $bankBits) {
                /** @var \Application\EntityRepository\Bank $bankEntityRepository */
                $bankEntityRepository = $entityManager->getRepository(Bank::class);
                //remove bit direction
                $bankBitsWithoutDirection = [];
                foreach ($bankBits as $bit => $value) {
                    if (strpos($bit, '_direction', 0) !== false) {
                        continue;
                    }
                    $bankBitsWithoutDirection[$bit] = $value;
                }
                $bankEntityRepository->saveBitsDBAL($data['deviceId'], $bankName, $bankBitsWithoutDirection);

                /** @var \Application\EntityRepository\EventLog $eventLogEntityRepository */
                $eventLogEntityRepository = $entityManager->getRepository(EventLog::class);
                /** @var EventLog $eventLog */
                $eventLog = $eventLogEntityRepository->saveLog($data['deviceId'], $bankName, $bankBits);

                $event = new Event();
                $event->setName(Event::EVENT_CONTACT_CLOSURE)
                    ->setDevice($data['deviceId'])
                    ->setBank($bankName)
                    ->setBits($bankBits)
                    ->setEventLog($eventLog);
                $eventManager->trigger(Event::EVENT_CONTACT_CLOSURE, $event);
            }
            $message->confirm();
        } catch (\Exception $e) {
            throw $e;
        }

        return true; //callback must return true!
    }

    /**
     * @param ContactClosureDaemon $daemon
     * @return null
     * @throws \Exception
     */
    public function contactClosureDaemonCycle($daemon)
    {
        $changes = [];
        try {
            $result = $daemon->getCommand()->{$daemon->getCommandAction()}();
        } catch (\Exception $e) {
            $daemon->err("Error: %s. Message: %s", get_class($e), $e->getMessage());
            return null;
        }
        foreach ($result as $bankName => $byte) {
            foreach ($byte as $bit => $value) {
                if (!array_key_exists($bankName, $daemon->statuses) || !array_key_exists($bit, $daemon->statuses[$bankName])) {
                    continue;
                }
                if ($daemon->statuses[$bankName][$bit] != $value) {
                    $direction = ($daemon->statuses[$bankName][$bit] > $value) ? 'down' : 'up';
                    //set cache
                    $daemon->statuses[$bankName] = $byte;
                    //set changes
                    $changes[$bankName][$bit.'_direction'] = $direction;
                    $changes[$bankName][$bit] = $value;
                }
            }
        }
        if (count($changes) > 0) {
            //send message to queue
            $daemon->getQueueMessage()->setData([
                'deviceId' => $daemon->getDevice()->getId(),
                'deviceBanks' => $changes
            ]);
            try {
                $daemon->getQueueServer()->send(
                    $daemon->getQueueMessage(),
                    'app.daemon.exchange',
                    $daemon->getQueueMessageRoutingKey()
                );
            } catch (\Exception $e) {
                $daemon->err("Queue send message failed: %s. Message: %s", get_class($e), $e->getMessage());
            }
        }
    }
}
