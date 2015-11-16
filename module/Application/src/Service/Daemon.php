<?php

namespace Application\Service;

use Application\Daemon\ContactClosureDaemon;
use Application\Entity\Bank;
use Doctrine\ORM\EntityManager;
use Kuai6\Queue\Message;
use Kuai6\Queue\Queue;
use Kuai6\Queue\Server;
use Kuai6\Queue\ServerFactory;
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
     */
    public function handleMainDaemonMessage($message)
    {
        try {
            /** @var EntityManager $entityManager */
            $entityManager = $this->getServiceLocator()->get('ApplicationEntityManager');
            $data = $message->getData();
            if (!array_key_exists('deviceId', $data) || !array_key_exists('deviceBanks', $data)) {
                return false;
            }
            foreach ($data['deviceBanks'] as $bankName => $bankBits) {
                $entityManager->getRepository(Bank::class)->saveBitsDBAL($data['deviceId'], $bankName, $bankBits);
            }
            $message->confirm();
        } catch (\Exception $e) {
        }

        return false; //callback must return false!
    }

    /**
     * @param ContactClosureDaemon $daemon
     * @throws \Exception
     */
    public function contactClosureDaemonCycle($daemon)
    {
        $changes = [];
        $result = $daemon->getCommand()->{$daemon->getCommandAction()}();
        foreach ($result as $bankName => $byte) {
            foreach ($byte as $bit => $value) {
                if ($daemon->statuses[$bankName][$bit] != $value) {
                    //set cache
                    $daemon->statuses[$bankName] = $byte;
                    //set changes
                    $changes[$bankName] = $byte;
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
